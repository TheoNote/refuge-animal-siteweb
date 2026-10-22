<?php

namespace App\Tests\Functional;

use App\Entity\Animal;
use App\Enum\AnimalStatus;
use App\Entity\User;
use App\Enum\RoleUser;
use App\Repository\AnimalRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnimalControllerTest extends WebTestCase
{
    public function testAnimalDetailPageIsReachableForExistingAnimal(): void
    {
        $client = static::createClient();
        $animalRepository = static::getContainer()->get(AnimalRepository::class);

        $animal = $animalRepository->findOneBy([]);
        self::assertNotNull($animal);

        $client->request('GET', '/animaux/' . $animal->getId());

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $animal->getName());
        self::assertSelectorTextContains('body', $animal->getSpecies());
        self::assertSelectorTextContains('body', $animal->getStatus()->value);
    }

    public function testAnimalDetailPageReturns404ForUnknownAnimal(): void
    {
        $client = static::createClient();
        $client->request('GET', '/animaux/999999');

        self::assertResponseStatusCodeSame(404);
    }

    public function testAnimalDetailPageShowsAdoptButtonOnlyForAvailable(): void
    {
        $client = static::createClient();
        $animalRepository = static::getContainer()->get(AnimalRepository::class);

        $available = new Animal();
        $available->setName('TestAdoptAvailable');
        $available->setSpecies('Chat');
        $available->setBreed('Test');
        $available->setBirthdate(new \DateTimeImmutable('2024-01-01'));
        $available->setDescription('Test animal available.');
        $available->setStatus(AnimalStatus::Available);
        $available->setEmoji('🐱');

        $adopted = new Animal();
        $adopted->setName('TestAdoptAdopted');
        $adopted->setSpecies('Chien');
        $adopted->setBreed('Test');
        $adopted->setBirthdate(new \DateTimeImmutable('2023-01-01'));
        $adopted->setDescription('Test animal adopted.');
        $adopted->setStatus(AnimalStatus::Adopted);
        $adopted->setEmoji('🐕');

        $animalRepository->persistAndSave($available);
        $animalRepository->persistAndSave($adopted);

        $client->request('GET', '/animaux/' . $available->getId());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Demander à adopter');

        $client->request('GET', '/animaux/' . $adopted->getId());
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('body', 'Adoption non disponible');

        $animalRepository->delete($animalRepository->find($available->getId()));
        $animalRepository->delete($animalRepository->find($adopted->getId()));
    }

    private function createUserByEmail(string $email, array $roles): User
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setEmail($email);
        $userService = static::getContainer()->get(UserService::class);
        $userService->register($user, 'Password123!');
        $user->setRoles($roles);
        static::getContainer()->get(UserRepository::class)->save($user);

        return $user;
    }

    public function testAnimalManagementPageRequiresWorkerRole(): void
    {
        $client = static::createClient();
        $user = $this->createUserByEmail(
            uniqid('admin_user_', true) . '@test.test',
            [RoleUser::User]
        );

        $client->loginUser($user, 'main');

        $client->request('GET', '/animaux-admin/');
        self::assertResponseStatusCodeSame(403);

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->delete($userRepository->find($user->getId()));
    }

    public function testWorkerCanViewAnimalManagementPage(): void
    {
        $client = static::createClient();
        $user = $this->createUserByEmail(
            uniqid('benvole_user_', true) . '@test.test',
            [RoleUser::Worker]
        );

        $client->loginUser($user, 'main');

        $client->request('GET', '/animaux-admin/');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Gestion des animaux');

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->delete($userRepository->find($user->getId()));
    }

    public function testWorkerCanCreateNewAnimal(): void
    {
        $client = static::createClient();
        $user = $this->createUserByEmail(
            uniqid('benvole_user2_', true) . '@test.test',
            [RoleUser::Worker]
        );

        $client->loginUser($user, 'main');

        $crawler = $client->request('GET', '/animaux-admin/nouveau');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $form->setValues([
            'animal[name]' => 'Rocky Test',
            'animal[species]' => 'Chien',
            'animal[breed]' => 'Test Race',
            'animal[birthdate]' => '2024-03-15',
            'animal[description]' => 'Test animal created by worker.',
            'animal[emoji]' => '🐶',
            'animal[status]' => '0',
        ]);

        $client->submit($form);

        self::assertResponseRedirects('/animaux-admin/');

        $animalRepository = static::getContainer()->get(AnimalRepository::class);
        $animal = $animalRepository->findByName('Rocky Test');
        self::assertNotNull($animal);
        self::assertSame(AnimalStatus::Available, $animal->getStatus());

        $animalRepository->delete($animalRepository->find($animal->getId()));

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->delete($userRepository->find($user->getId()));
    }
}

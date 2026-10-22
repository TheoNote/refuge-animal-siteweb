<?php

namespace App\Tests\Functional;

use App\Entity\Animal;
use App\Enum\AnimalStatus;
use App\Repository\AnimalRepository;
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
        $entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
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

        $animal = $animalRepository->find($available->getId());
        if ($animal)
            $animalRepository->delete($animal);
        $animal = $animalRepository->find($adopted->getId());
        if ($animal)
            $animalRepository->delete($animal);
    }
}


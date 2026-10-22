<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Enum\RoleUser;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminUserCrudTest extends WebTestCase
{
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

    public function testAdminUserPageRequiresAdminRole(): void
    {
        $client = static::createClient();
        $user = $this->createUserByEmail(
            uniqid('worker_user_', true) . '@test.test',
            [RoleUser::Worker]
        );

        $client->loginUser($user, 'main');

        $client->request('GET', '/admin-users/');
        self::assertResponseStatusCodeSame(403);

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->delete($userRepository->find($user->getId()));
    }

    public function testAdminCanViewUserManagementPage(): void
    {
        $client = static::createClient();
        $admin = $this->createUserByEmail(
            uniqid('admin_user_', true) . '@test.test',
            [RoleUser::Admin]
        );

        $client->loginUser($admin, 'main');

        $client->request('GET', '/admin-users/');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Gestion des utilisateurs');
        self::assertSelectorTextContains('body', $admin->getEmail());

        $userRepository = static::getContainer()->get(UserRepository::class);
        $userRepository->delete($userRepository->find($admin->getId()));
    }

    public function testAdminCanEditUser(): void
    {
        $client = static::createClient();
        $admin = $this->createUserByEmail(
            uniqid('admin_edit_', true) . '@test.test',
            [RoleUser::Admin]
        );
        $target = $this->createUserByEmail(
            uniqid('target_user_', true) . '@test.test',
            [RoleUser::User]
        );

        $client->loginUser($admin, 'main');

        $crawler = $client->request('GET', '/admin-users/' . $target->getId());
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Enregistrer')->form();
        $form->setValues([
            'user_admin[username]' => 'updateduser',
            'user_admin[email]' => $target->getEmail(),
        ]);

        $client->submit($form);

        self::assertResponseRedirects('/admin-users/');

        $userRepository = static::getContainer()->get(UserRepository::class);
        $updated = $userRepository->find($target->getId());
        self::assertSame('updateduser', $updated->getUsername());

        $userRepository->delete($userRepository->find($admin->getId()));
        $userRepository->delete($userRepository->find($target->getId()));
    }

    public function testAdminCannotDeleteOwnAccount(): void
    {
        $client = static::createClient();
        $admin = $this->createUserByEmail(
            uniqid('admin_self_', true) . '@test.test',
            [RoleUser::Admin]
        );

        $client->loginUser($admin, 'main');

        $client->request('GET', '/admin-users/');
        self::assertResponseIsSuccessful();

        $client->request('POST', '/admin-users/' . $admin->getId() . '/supprimer', [
            'csrf_token' => 'invalid-token',
        ], [], ['HTTP_ORIGIN' => 'http://localhost']);

        self::assertResponseRedirects('/admin-users/');

        $userRepository = static::getContainer()->get(UserRepository::class);
        self::assertNotNull($userRepository->find($admin->getId()));

        $userRepository->delete($userRepository->find($admin->getId()));
    }

    public function testAdminCanDeleteOtherUser(): void
    {
        $client = static::createClient();
        $admin = $this->createUserByEmail(
            uniqid('admin_del_', true) . '@test.test',
            [RoleUser::Admin]
        );
        $target = $this->createUserByEmail(
            uniqid('del_target_', true) . '@test.test',
            [RoleUser::User]
        );

        $client->loginUser($admin, 'main');

        $crawler = $client->request('GET', '/admin-users/');
        self::assertResponseIsSuccessful();

        $form = $crawler->filter(sprintf(
            'form[action="/admin-users/%d/supprimer"]',
            $target->getId()
        ));
        $csrfToken = $form->filter('input[name="csrf_token"]')->attr('value');

        $client->request('POST', '/admin-users/' . $target->getId() . '/supprimer', [
            'csrf_token' => $csrfToken,
        ], [], ['HTTP_ORIGIN' => 'http://localhost']);

        self::assertResponseRedirects('/admin-users/');

        $userRepository = static::getContainer()->get(UserRepository::class);
        self::assertNull($userRepository->find($target->getId()));

        $userRepository->delete($userRepository->find($admin->getId()));
    }
}

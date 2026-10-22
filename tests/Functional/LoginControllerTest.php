<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{

    public function testLoginPageIsReachable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Se connecter');
    }

    public function testLoginValidRedirectsToHome(): void
    {
        $email = uniqid('login_valid_', true) . '@test.test';

        $user = new User();
        $user->setUsername('loginuser');
        $user->setEmail($email);
        $client = static::createClient();

        $userService = static::getContainer()->get(UserService::class);

        $userService->register($user, 'Password123!');

        $client->request('POST', '/connexion', [
            '_username' => $email,
            '_password' => 'Password123!',
            '_csrf_token' => 'csrf-token',
        ], [], ['HTTP_ORIGIN' => 'http://localhost']);

        self::assertResponseRedirects('/');
        $client->followRedirect();
        self::assertSame($email, $client->getCrawler()->filter('.nav-user')->text());

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findUserByEmail($email);
        if($user)
            $userRepository->delete($user);
    }

    public function testLoginWithInvalidCredentialsShowsFrenchError(): void
    {
        $email = uniqid('login_invalid_', true) . '@test.test';

        $client = static::createClient();

        $user = new User();
        $user->setUsername('loginuser');
        $user->setEmail($email);

        $userService = static::getContainer()->get(UserService::class);

        $userService->register($user, 'Password123!');

        $client->request('POST', '/connexion', [
            '_username' => $email,
            '_password' => 'wrong-password',
            '_csrf_token' => 'csrf-token',
        ], [], ['HTTP_ORIGIN' => 'http://localhost']);

        self::assertResponseRedirects('/connexion');
        $client->followRedirect();
        self::assertSelectorTextContains('.alert-error', 'Identifiants invalides.');

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findUserByEmail($email);
        if($user)
            $userRepository->delete($user);
    }
}

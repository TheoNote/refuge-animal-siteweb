<?php

namespace App\Tests\Functional;

use App\Enum\RoleUser;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    public function testRegisterPageIsReachable(): void
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Créer un compte');
    }

    public function testWeakPasswordDisplaysValidationMessage(): void
    {
        $email = uniqid('register_weak_', true) . '@test.test';

        $client = static::createClient();
        $client->request('POST', '/inscription', [
            'user' => [
                'username' => 'aa',
                'email' => $email,
                'password' => [
                    'first' => 'aa',
                    'second' => 'aa',
                ],
                '_token' => 'csrf-token',
            ],
        ], [], ['HTTP_ORIGIN' => 'http://localhost']);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('.alert-error', 'Merci de corriger les erreurs ci-dessous.');
        self::assertSelectorTextContains('body', 'Le mot de passe est trop faible');
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findUserByEmail($email);
        self::assertNull($user);
        if($user)
            $userRepository->delete($user);
    }

    public function testValidRegistrationCreatesUserAndRedirects(): void
    {
        $email = uniqid('register_valid_', true) . '@test.test';

        $client = static::createClient();
        $client->request('POST', '/inscription', [
            'user' => [
                'username' => 'validuser',
                'email' => $email,
                'password' => [
                    'first' => 'Str0ng!Passw0rd#2026',
                    'second' => 'Str0ng!Passw0rd#2026',
                ],
                '_token' => 'csrf-token',
            ],
        ], [], ['HTTP_ORIGIN' => 'http://localhost']);

        self::assertResponseRedirects('/');
        $client->followRedirect();
        self::assertSelectorTextContains('.alert-success', 'Votre compte a bien été créé.');

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findUserByEmail($email);

        self::assertNotNull($user);
        self::assertSame('validuser', $user->getUsername());
        self::assertNotSame('Str0ng!Passw0rd#2026', $user->getPassword());
        self::assertContains(RoleUser::User->value, $user->getRoles());

        if($user)
            $userRepository->delete($user);
    }
}

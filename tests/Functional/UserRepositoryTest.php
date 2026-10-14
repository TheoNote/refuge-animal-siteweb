<?php

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private ?UserRepository $userRepository;
    private array $emails = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $this->userRepository = static::getContainer()
            ->get(UserRepository::class);
    }

    public function testPersistAndSaveAndFindUserByEmail(): void
    {
        $email = uniqid('user_', true) . '@test.test';
        $this->emails[] = $email;
        $user = new User();
        $user->setUsername('test');
        $user->setEmail($email);
        $user->setPassword('hashed');

        $this->userRepository->persistAndSave($user);

        $this->assertSame($user, $this->userRepository->findUserByEmail($email));
    }

    public function testFindUserByEmailReturnNull(): void
    {
        $this->assertNull($this->userRepository->findUserByEmail('empty@test.test'));
    }

    public function testDeleteRemovesUser(): void
    {
        $email = uniqid('user_', true) . '@test.test';
        $this->emails[] = $email;
        $user = new User();
        $user->setUsername('test');
        $user->setEmail($email);
        $user->setPassword('hashed');

        $this->userRepository->persistAndSave($user);
        self::assertSame($user, $this->userRepository->findUserByEmail($email));

        $this->userRepository->delete($user);

        self::assertNull($this->userRepository->findUserByEmail($email));
    }
    protected function tearDown(): void
    {
        foreach ($this->emails as $email)
        {
            $user = $this->userRepository->findUserByEmail($email);
            if ($user)
                $this->userRepository->delete($user);
        }

        parent::tearDown();
        //$this->userRepository->close();
        $this->userRepository = null;
    }
}

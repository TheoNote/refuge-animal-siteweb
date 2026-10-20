<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Enum\RoleUser;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    public function testRegisterUser(): void
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->expects($this->once())
            ->method('persistAndSave')
            ->with($this->isInstanceOf(User::class));

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasher->expects($this->once())
            ->method('hashPassword')
            ->with($this->isInstanceOf(User::class), 'test')
            ->willReturn('hashed_password');

        $service = new UserService($userRepository, $passwordHasher);

        $user = new User();
        $user->setEmail('test@test.test');
        $user->setUsername('test');

        $result = $service->register($user, 'test');

        $this->assertSame($user, $result);
        $this->assertSame('test@test.test', $user->getEmail());
        $this->assertSame('test', $user->getUsername());
        $this->assertSame('hashed_password', $user->getPassword());
        $this->assertContains(RoleUser::User, $user->getRoles());
    }
}

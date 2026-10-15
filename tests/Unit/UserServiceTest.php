<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Enum\RoleUser;
use App\Repository\UserRepository;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    public function testRegisterCreatesUser(): void
    {
        $repository = $this->createMock(UserRepository::class);
        $repository->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $service = new UserService($repository);
        $email = 'test@test.test';
        $userName = 'test';
        $password = 'test';
        $role=RoleUser::Worker;
        $user = $service->register($email, $userName, $password, [$role]);

        $this->assertSame($email, $user->getEmail());
        $this->assertSame($userName, $user->getUsername());
        $this->assertSame($password, $user->getPassword());
        $this->assertContains($role, $user->getRoles());
    }
}

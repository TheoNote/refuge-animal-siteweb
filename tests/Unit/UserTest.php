<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Enum\RoleUser;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testImplementsExpectedInterfaces(): void
    {
        $this->assertInstanceOf(UserInterface::class, $this->user);
        $this->assertInstanceOf(PasswordAuthenticatedUserInterface::class, $this->user);
    }

    public function testConstructorInit(): void
    {
        $this->assertNull($this->user->getId());
        $this->assertInstanceOf(DateTimeImmutable::class , $this->user->getCreatedAt());
    }

    public function testUserContainUserRole(): void
    {
        $this->assertContains(RoleUser::User, $this->user->getRoles());
    }


    public function testUsernameGetterAndSetter(): void
    {
        $this->user->setUsername('test');
        $this->assertSame('test', $this->user->getUsername());
        $this->assertIsString($this->user->getUsername());
    }

    public function testPasswordGetterAndSetter(): void
    {
        $this->user->setPassword('test');
        $this->assertSame('test', $this->user->getPassword());
        $this->assertIsString($this->user->getPassword());
    }

    public function testRolesGetterAndSetter(): void
    {
        $this->user->setRoles([RoleUser::User]);
        $this->assertSame([RoleUser::User], $this->user->getRoles());
        $this->assertIsArray($this->user->getRoles());
    }

    public function testEmailGetterAndSetter(): void
    {
        $this->user->setEmail('test');
        $this->assertSame('test', $this->user->getEmail());
        $this->assertIsString($this->user->getEmail());
    }
}

<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct
    (
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function register(User $user, string $plainPassword) : User
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));

        $this->userRepository->persistAndSave($user);

        return $user;
    }
}

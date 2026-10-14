<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct
    (
        private UserRepository $userRepository,
    )
    {
    }

    public function register(string $email, string $username, string $plainPassword, ?array $role) : ?User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($plainPassword);
        $user->setRoles([$role]);

        $this->userRepository->persist($user);
        return $user;
    }
}

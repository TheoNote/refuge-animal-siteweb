<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\RoleUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'username' => 'Utilisateur',
                'email' => 'user@refuge.local',
                'password' => 'password',
                'roles' => [RoleUser::User],
            ],
            [
                'username' => 'Bénévole',
                'email' => 'benvole@refuge.local',
                'password' => 'password',
                'roles' => [RoleUser::Worker],
            ],
            [
                'username' => 'Administrateur',
                'email' => 'admin@refuge.local',
                'password' => 'password',
                'roles' => [RoleUser::Admin],
            ],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
            $user->setRoles($data['roles']);
            $manager->persist($user);
        }

        $manager->flush();
    }
}

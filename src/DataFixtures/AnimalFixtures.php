<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Enum\AnimalStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AnimalFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $animals = [
            [
                'name' => 'Milo',
                'species' => 'Chien',
                'breed' => 'Berger australien',
                'birthdate' => new \DateTimeImmutable('2024-05-10'),
                'description' => 'Joueur et affectueux, Milo cherche une famille active.',
                'status' => AnimalStatus::Available,
                'emoji' => '🐕',
            ],
            [
                'name' => 'Nala',
                'species' => 'Chat',
                'breed' => 'Européen',
                'birthdate' => new \DateTimeImmutable('2024-11-20'),
                'description' => 'Douce et curieuse, Nala adore les câlins.',
                'status' => AnimalStatus::Available,
                'emoji' => '🐈',
            ],
            [
                'name' => 'Rex',
                'species' => 'Chien',
                'breed' => 'Labrador',
                'birthdate' => new \DateTimeImmutable('2021-06-15'),
                'description' => "Calme et obéissant, Rex est une demande d'adoption en cours.",
                'status' => AnimalStatus::Pending,
                'emoji' => '🐕‍🦺',
            ],
            [
                'name' => 'Luna',
                'species' => 'Chat',
                'breed' => 'Siamois',
                'birthdate' => new \DateTimeImmutable('2023-02-01'),
                'description' => 'Luna a trouvé sa famille et a rejoint son nouveau foyer.',
                'status' => AnimalStatus::Adopted,
                'emoji' => '🐱',
            ],
            [
                'name' => 'Coco',
                'species' => 'Oiseau',
                'breed' => 'Gris du Gabon',
                'birthdate' => new \DateTimeImmutable('2019-04-20'),
                'description' => 'Coco est sociable et bavard, il cherche un foyer patient.',
                'status' => AnimalStatus::Available,
                'emoji' => '🦜',
            ],
            [
                'name' => 'Biscuit',
                'species' => 'Lapin',
                'breed' => 'Nain bélier',
                'birthdate' => new \DateTimeImmutable('2025-01-10'),
                'description' => 'Petit et tranquille, Biscuit convient très bien aux enfants.',
                'status' => AnimalStatus::Available,
                'emoji' => '🐰',
            ],
        ];

        foreach ($animals as $data) {
            $animal = new Animal();
            $animal->setName($data['name']);
            $animal->setSpecies($data['species']);
            $animal->setBreed($data['breed']);
            $animal->setBirthdate($data['birthdate']);
            $animal->setDescription($data['description']);
            $animal->setStatus($data['status']);
            $animal->setEmoji($data['emoji']);
            $manager->persist($animal);
        }

        $manager->flush();
    }
}

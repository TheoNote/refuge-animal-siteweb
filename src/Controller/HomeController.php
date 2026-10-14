<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $animals = [
            [
                'name' => 'Milo',
                'species' => 'Chien',
                'breed' => 'Berger australien',
                'birthdate' => new \DateTimeImmutable('2024-05-10'),
                'description' => 'Joueur et affectueux, Milo cherche une famille active.',
                'status' => 'Disponible',
                'emoji' => '🐕',
            ],
            [
                'name' => 'Nala',
                'species' => 'Chat',
                'breed' => 'Européen',
                'birthdate' => new \DateTimeImmutable('2024-11-20'),
                'description' => 'Douce et curieuse, Nala adore les câlins.',
                'status' => 'Disponible',
                'emoji' => '🐈',
            ],
            [
                'name' => 'Rex',
                'species' => 'Chien',
                'breed' => 'Labrador',
                'birthdate' => new \DateTimeImmutable('2021-06-15'),
                'description' => 'Calme et obéissant, Rex est une demande d\'adoption en cours.',
                'status' => 'En cours d\'adoption',
                'emoji' => '🐕‍🦺',
            ],
            [
                'name' => 'Luna',
                'species' => 'Chat',
                'breed' => 'Siamois',
                'birthdate' => new \DateTimeImmutable('2023-02-01'),
                'description' => 'Luna a trouvé sa famille et a rejoint son nouveau foyer.',
                'status' => 'Adopté',
                'emoji' => '🐱',
            ],
            [
                'name' => 'Coco',
                'species' => 'Oiseau',
                'breed' => 'Gris du Gabon',
                'birthdate' => new \DateTimeImmutable('2019-04-20'),
                'description' => 'Coco est sociable et bavard, il cherche un foyer patient.',
                'status' => 'Disponible',
                'emoji' => '🦜',
            ],
            [
                'name' => 'Biscuit',
                'species' => 'Lapin',
                'breed' => 'Nain bélier',
                'birthdate' => new \DateTimeImmutable('2025-01-10'),
                'description' => 'Petit et tranquille, Biscuit convient très bien aux enfants.',
                'status' => 'Disponible',
                'emoji' => '🐰',
            ],
        ];

        return $this->render('home/index.html.twig', [
            'animals' => $animals,
        ]);
    }
}

<?php

namespace App\Controller\Animal;

use App\Entity\Animal;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class ShowController extends AbstractController
{
    #[Route('/animaux/{id}', name: 'app_animal_show', requirements: ['id' => '\d+'])]
    public function __invoke(Animal $animal): Response
    {
        return $this->render('animal/show.html.twig', [
            'animal' => $animal,
        ]);
    }
}

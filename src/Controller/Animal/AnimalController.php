<?php

namespace App\Controller\Animal;

use App\Entity\Animal;
use App\Form\Animal\AnimalType;
use App\Repository\AnimalRepository;
use App\Service\AnimalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route('/animaux-admin')]
#[IsGranted('ROLE_WORKER')]
class AnimalController extends AbstractController
{
    public function __construct(private AnimalRepository $animalRepository, private AnimalService $animalService)
    {
    }

    #[Route('/', name: 'app_animal_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('animal/index.html.twig', [
            'animals' => $this->animalRepository->findAllOrderedByName(),
        ]);
    }

    #[Route('/nouveau', name: 'app_animal_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $animal = new Animal();
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->animalService->create($form->getData());
            $this->addFlash('success', 'L\'animal a bien été créé.');

            return $this->redirect($this->generateUrl('app_animal_index'));
        }

        $response = new Response();
        $response->setStatusCode($form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);

        return $this->render('animal/form.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
        ], $response);
    }

    #[Route('/{id}', name: 'app_animal_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Animal $animal): Response
    {
        $form = $this->createForm(AnimalType::class, $animal);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->animalService->update($form->getData());
            $this->addFlash('success', 'L\'animal a bien été modifié.');

            return $this->redirect($this->generateUrl('app_animal_index'));
        }

        $response = new Response();
        $response->setStatusCode($form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);

        return $this->render('animal/form.html.twig', [
            'animal' => $animal,
            'form' => $form->createView(),
        ], $response);
    }

    #[Route('/{id}/supprimer', name: 'app_animal_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Animal $animal): Response
    {
        $csrfToken = $request->request->get('csrf_token', '');
        if ($csrfToken && $this->isCsrfTokenValid('delete' . $animal->getId(), $csrfToken)) {
            $this->animalService->delete($animal);
            $this->addFlash('success', 'L\'animal a bien été supprimé.');
        }

        return $this->redirect($this->generateUrl('app_animal_index'));
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\UserAdminType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AsController]
#[Route('/admin-users')]
#[IsGranted('ROLE_ADMIN')]
class UserAdminController extends AbstractController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $this->userRepository->findAllOrderedByEmail(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->persistAndSave($form->getData());
            $this->addFlash('success', 'L\'utilisateur a bien été modifié.');

            return $this->redirect($this->generateUrl('app_admin_user_index'));
        }

        $response = new Response();
        $response->setStatusCode($form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK);

        return $this->render('admin/user/form.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ], $response);
    }

    #[Route('/{id}/supprimer', name: 'app_admin_user_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, User $user): Response
    {
        if ($user->getId() === $this->getUser()?->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        } else {
            $csrfToken = $request->request->get('csrf_token', '');
            if ($csrfToken && $this->isCsrfTokenValid('delete' . $user->getId(), $csrfToken)) {
                $this->userRepository->delete($user);
                $this->addFlash('success', 'L\'utilisateur a bien été supprimé.');
            }
        }

        return $this->redirect($this->generateUrl('app_admin_user_index'));
    }
}

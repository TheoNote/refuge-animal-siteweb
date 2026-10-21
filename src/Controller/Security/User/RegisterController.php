<?php

namespace App\Controller\Security\User;

use App\Entity\User;
use App\Form\User\UserType;
use App\Service\UserService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

#[AsController]
#[Route(path: '/inscription', name: 'register_user', methods: ['GET', 'POST'])]
class RegisterController
{
    public function __invoke(Environment $twig, UserService $userService, Request $request, FormFactoryInterface $formFactory, RouterInterface $router): Response {
        $status = Response::HTTP_OK;

        $user = new User();
        $form = $formFactory->create(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userService->register($user, $form->get('password')->get('first')->getData());

            $request->getSession()->getFlashBag()->add('success', 'Votre compte a bien été créé.');

            return new RedirectResponse($router->generate('app_home'));
        }else if ($form->isSubmitted())
        {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        return new Response($twig->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]), $status);
    }
}

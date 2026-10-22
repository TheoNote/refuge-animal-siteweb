<?php

namespace App\Controller\Security\User;

use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/deconnexion', name: 'app_logout')]
class LogoutController
{
    public function __invoke(): never
    {
        throw new \LogicException('This controller is only intercepted by the logout firewall listener.');
    }
}

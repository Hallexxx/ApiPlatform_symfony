<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LogoutController
{
    #[Route('/logout', name: 'logout')]
    public function logout(): RedirectResponse
    {
        $session->remove('auth_token');
        return new RedirectResponse('/');
    }
}

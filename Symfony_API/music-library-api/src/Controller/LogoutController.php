<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LogoutController extends AbstractController
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): RedirectResponse
    {
        if ($this->session) {
            // Invalide la session complète, ce qui supprime aussi le token de sécurité
            $this->session->invalidate();
        }
        return new RedirectResponse('/');
    }
}

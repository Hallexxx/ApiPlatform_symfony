<?php

// src/Controller/LogoutController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController
{
    #[Route('/logout', name: 'logout')]
    public function logout(): RedirectResponse
    {
        // Cette méthode sera appelée lorsqu'un utilisateur appuie sur un bouton pour se déconnecter
        // Symfony ne traite pas directement la déconnexion ici, mais redirige plutôt l'utilisateur.

        // En utilisant le service de session ou en supprimant directement les cookies/token, on peut gérer la déconnexion côté client.
        return new RedirectResponse('/');
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // Exemple de validation simple de l'utilisateur (remplacez par votre logique d'authentification)
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];

        // Si l'authentification est correcte, générez un token JWT
        if ($email === 'user@example.com' && $password === 'password') {
            // Générer le token JWT ici (à adapter avec la bibliothèque JWT de Symfony)
            $token = 'fake-jwt-token';
            return new JsonResponse(['token' => $token]);
        }

        return new JsonResponse(['error' => 'Invalid credentials'], 401);
    }
}

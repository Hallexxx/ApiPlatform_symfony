<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, EntityManagerInterface $entityManager): JsonResponse
    {
        // Récupérer les données JSON envoyées dans la requête
        $data = json_decode($request->getContent(), true);

        // Vérifier que l'email, le mot de passe et le nom d'utilisateur sont présents
        if (!isset($data['email'], $data['password'], $data['username'])) {
            return new JsonResponse(['error' => 'Email, password, and username are required'], 400);
        }

        // Créer un nouvel utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);

        // Hasher le mot de passe avec la méthode hashPassword
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

        // Valider les données de l'utilisateur
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => 'Invalid input data'], 400);
        }

        // Sauvegarder l'utilisateur dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        // Répondre que l'utilisateur a été créé avec succès
        return new JsonResponse(['message' => 'User created successfully'], 201);
    }

    #[Route('/register/form', name: 'register_form')]
    public function showRegisterForm(): Response
    {
        return $this->render('login-register/register.html.twig');
    }
}

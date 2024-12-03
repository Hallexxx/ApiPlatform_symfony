<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginController extends AbstractController
{
    private $jwtTokenManager;
    private $passwordHasher;
    private $entityManager;

    public function __construct(
        JWTTokenManagerInterface $jwtTokenManager, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager
    ) {
        $this->jwtTokenManager = $jwtTokenManager;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function api_login(Request $request, UserPasswordHasherInterface $passwordHasher, TokenStorageInterface $tokenStorage, ManagerRegistry $doctrine): JsonResponse
    {
        if ($this->getUser()) {
            return $this->json([
                'message' => 'Vous êtes déjà connecté.'
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $email = $request->get('email');
        $password = $request->get('password');

        $user = $doctrine->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json([
                'message' => 'Identifiants invalides (Email)'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json([
                'message' => 'Identifiants invalides (Mot de passe)'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }


        $jwt = $this->jwtManager->create($user);

        $session = $this->requestStack->getSession();

        $session->set('token', $jwt);
        $session->set('username', $user->getUsername());

        return $this->json([
            'message' => 'Connecté avec succès. User ID: ' . $user->getId(),
            'token'=> $jwt
        ], JsonResponse::HTTP_OK);
    }


    #[Route('/login/form', name: 'login_form')]
    public function showLoginForm()
    {
        return $this->render('login-register/login.html.twig');
    }
}
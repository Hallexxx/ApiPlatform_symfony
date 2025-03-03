<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LoginController extends AbstractController
{
    private $jwtTokenManager;
    private $passwordHasher;
    private $entityManager;
    private $session;
    private $requestStack;

    public function __construct(
        JWTTokenManagerInterface $jwtTokenManager,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        RequestStack $requestStack,
    ) {
        $this->jwtTokenManager = $jwtTokenManager;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    // #[Route('/login', name: 'login', methods: ['POST'])]
    // public function login(
    //     Request $request,
    //     JWTTokenManagerInterface $jwtTokenManager
    // ): Response {
    //     $session = $this->requestStack->getSession(); 
        
    //     $email = $request->request->get('email');
    //     $password = $request->request->get('password');

    //     if (!$email || !$password) {
    //         return new JsonResponse(['error' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
    //     }

    //     $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

    //     if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
    //         return new JsonResponse(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
    //     }

    //     $token = $jwtTokenManager->create($user);

    //     $session->clear(); 
    //     $session->set('auth_token', $token);
    //     $session->set('user_id', $user->getId());

    //     return $this->redirectToRoute('music_library');
    // }


    #[Route('/login', name: 'login_form')]
    public function showLoginForm()
    {
        return $this->render('login-register/login.html.twig');
    }

    #[Route('/session-debug', name: 'session_debug')]
    public function sessionDebug(): JsonResponse
    {
        $session = $this->requestStack->getSession(); 

        return new JsonResponse([
            'user_id' => $session->get('user_id'),
            'username' => $session->get('username'),
            'token' => $session->get('auth_token'),
        ]);
    }

}
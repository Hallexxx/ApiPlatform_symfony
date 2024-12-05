<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthController extends AbstractController
{
    private $jwtTokenManager;
    private $session;

    public function __construct(JWTTokenManagerInterface $jwtTokenManager, RequestStack $requestStack)
    {
        $this->jwtTokenManager = $jwtTokenManager;
        $this->session = $requestStack->getSession();
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer les données du formulaire
        $email = $request->request->get('email');
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        if (!$email || !$username || !$password) {
            return new JsonResponse(['error' => 'Email, username, and password are required'], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($passwordHasher->hashPassword($user, $password));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->render('login-register/register.html.twig', [
                'errors' => $errors,
            ]);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        // Générez un token JWT pour l'utilisateur
        $token = $this->jwtTokenManager->create($user);

        // Redirigez vers la page d'accueil avec le token
        return $this->redirectToRoute('music_library', [
            'token' => $token,
        ]);
    }


    #[Route('/register/form', name: 'register_form')]
    public function showRegisterForm(): Response
    {
        return $this->render('login-register/register.html.twig');
    }
}

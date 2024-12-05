<?php

// MusicLibraryController.php
namespace App\Controller;

use App\Service\MusicLibraryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class MusicLibraryController extends AbstractController
{
    private $musicLibraryService;
    private $entityManager;

    public function __construct(MusicLibraryService $musicLibraryService, EntityManagerInterface $entityManager)
    {
        $this->musicLibraryService = $musicLibraryService;
        $this->entityManager = $entityManager;
        
    }

    #[Route('/', name: 'music_library')]
    public function index(SessionInterface $session): Response
    {
        $token = $session->get('auth_token');
        $userId = $session->get('user_id');
        
        $isAuthenticated = $token !== null && $userId !== null;

        $user = $this->entityManager->getRepository(User::class)->find($userId);
        
        if (!$user || !$token) {
            return $this->redirectToRoute('login_form');
        }

        $songs = $this->musicLibraryService->getAllSongs();
        $albums = $this->musicLibraryService->getAllAlbums();
        $artists = $this->musicLibraryService->getAllArtists();

        return $this->render('music_library/index.html.twig', [
            'songs' => $songs,
            'albums' => $albums,
            'artists' => $artists,
            'is_authenticated' => $isAuthenticated,
            'username' => $user->getUsername(),
        ]);
    }


}


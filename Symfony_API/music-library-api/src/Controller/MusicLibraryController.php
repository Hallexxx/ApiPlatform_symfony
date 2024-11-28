<?php

// MusicLibraryController.php
namespace App\Controller;

use App\Service\MusicLibraryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MusicLibraryController extends AbstractController
{
    private $musicLibraryService;

    public function __construct(MusicLibraryService $musicLibraryService)
    {
        $this->musicLibraryService = $musicLibraryService;
    }

    #[Route('/', name: 'music_library')]
    public function index(): Response
    {
        $songs = $this->musicLibraryService->getAllSongs();
        $albums = $this->musicLibraryService->getAllAlbums();
        $artists = $this->musicLibraryService->getAllArtists();

        return $this->render('music_library/index.html.twig', [
            'songs' => $songs,
            'albums' => $albums,
            'artists' => $artists,
        ]);
    }



}


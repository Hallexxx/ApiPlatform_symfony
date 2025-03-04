<?php

// MusicLibraryController.php
namespace App\Controller;

use App\Service\MusicLibraryService;
use App\Service\SongService;
use App\Service\AlbumService;
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
    public function index(
        SessionInterface $session, 
        SongService $songService, 
        AlbumService $albumService
    ): Response {
        $token = $session->get('auth_token');
        $userId = $session->get('user_id');

        $user = $userId !== null ? $this->entityManager->getRepository(User::class)->find($userId) : null;
        $isAuthenticated = $token !== null && $user !== null;

        $artists = $this->musicLibraryService->getAllArtists();
        $limitedSongsData = $songService->getLimitedSongs(10);
        $limitedAlbumsData = $albumService->getLimitedAlbums(10);

        $favoritedSongIds = [];
        $favoritedArtistIds = [];
        $favoritedAlbumIds = [];
        if ($user) {
            foreach ($user->getFavories() as $favoris) {
                if ($favoris->getSong()) {
                    $favoritedSongIds[] = $favoris->getSong()->getId();
                }
                if ($favoris->getArtist()) {
                    $favoritedArtistIds[] = $favoris->getArtist()->getId();
                }
                if ($favoris->getAlbum()) {
                    $favoritedAlbumIds[] = $favoris->getAlbum()->getId();
                }
            }
        }

        return $this->render('music_library/index.html.twig', [
            'limitedSongsData'  => $limitedSongsData,
            'limitedAlbumsData' => $limitedAlbumsData,
            'artists'           => $artists,
            'is_authenticated'  => $isAuthenticated,
            'username'          => $user ? $user->getUsername() : null,
            'favoritedSongIds'  => $favoritedSongIds,
            'favoritedArtistIds'=> $favoritedArtistIds,
            'favoritedAlbumIds' => $favoritedAlbumIds,
        ]);
    }


}

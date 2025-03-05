<?php

namespace App\Controller;

use App\Entity\Song;
use App\Service\SongService;
use App\Service\ArtistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;


class SongController extends AbstractController
{
    private SongService $songService;
    private ArtistService $artistService;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SongService $songService, 
        ArtistService $artistService,
        EntityManagerInterface $entityManager
    ) {
        $this->songService = $songService;
        $this->artistService = $artistService;
        $this->entityManager = $entityManager;
    }

    #[Route('/songs', name: 'song_page', methods: ['GET'])]
    public function listSongs(SessionInterface $session): Response
    {
        $songs = $this->songService->getAllSongs();
        $artists = $this->artistService->getAllArtists();

        /** @var User|null $user */
        $user = $this->getUser();
        $favoritedSongIds = [];
        if ($user instanceof User) {
            foreach ($user->getFavories() as $favoris) {
                if ($favoris->getSong()) {
                    $favoritedSongIds[] = $favoris->getSong()->getId();
                }
            }
        }

        $isAuthenticated = $user !== null;

        return $this->render('song/song.html.twig', [
            'songs'             => $songs,
            'artists'           => $artists,
            'favoritedSongIds'  => $favoritedSongIds,
            'isAuthenticated'   => $isAuthenticated,
        ]);
    }

    #[Route('/artists/{artistId}/albums/{albumIndex}/songs/{songIndex}', name: 'song_details', methods: ['GET'])]
    public function showSongDetails(int $artistId, int $albumIndex, int $songIndex): Response
    {
        try {
            $artist = $this->artistService->getArtistWithAlbumsAndSongs($artistId);
            if (!$artist) {
                throw $this->createNotFoundException('Artist not found.');
            }
            $albums = $artist->getAlbums();
            if (!isset($albums[$albumIndex - 1])) {
                throw $this->createNotFoundException('Album not found at the specified index.');
            }
            $album = $albums[$albumIndex - 1];

            $songs = $album->getSongs();
            if (!isset($songs[$songIndex - 1])) {
                throw $this->createNotFoundException('Song not found at the specified index.');
            }
            $song = $songs[$songIndex - 1];

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->render('song/song_details.html.twig', [
            'song' => $song,
            'album' => $album,
            'artist' => $artist,
        ]);
    }

    public function deleteSong(int $id): Response
    {
        try {
            $this->songService->deleteSong($id);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => 'Song deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}

<?php

namespace App\Controller;

use App\Entity\Song;
use App\Service\SongService;
use App\Service\ArtistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Handles song-specific operations.
 */
class SongController extends AbstractController
{
    private SongService $songService;

    public function __construct(SongService $songService, ArtistService $artistService)
    {
        $this->songService = $songService;
        $this->artistService = $artistService;
    }

    
    #[Route('/songs', name: 'song_page', methods: ['GET'])]
    public function listSongs(): Response
    {
        // Récupère toutes les chansons (ajuste selon ton modèle)
        $songs = $this->songService->getAllSongs();

        // Récupère également les artistes, albums et autres données nécessaires
        $artists = $this->artistService->getAllArtists();

        return $this->render('song/song.html.twig', [
            'songs' => $songs,
            'artists' => $artists, // Assure-toi que 'artists' est transmis
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






    /**
     * Deletes a specific song.
     *
     * @Route('/songs/{id}', name: 'song_delete', methods: ['DELETE'])
     */
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

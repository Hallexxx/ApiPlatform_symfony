<?php

namespace App\Controller;

use App\Entity\Song;
use App\Service\SongService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Handles song-specific operations.
 */
class SongController extends AbstractController
{
    private SongService $songService;

    public function __construct(SongService $songService)
    {
        $this->songService = $songService;
    }

    
    #[Route('/songs', name: 'song_page', methods: ['GET'])]
    public function listSongs(): Response
    {
        $songs = $this->songService->getAllSongs();

        return $this->render('song/song.html.twig', [
            'songs' => $songs,
        ]);
    }

    #[Route('/artists/{artistId}/albums/{albumId}/songs/{songId}', name: 'song_details', methods: ['GET'])]
    public function showSongDetails(int $artistId, int $albumId, int $songId): Response
    {
        try {
            $song = $this->songService->getSongById($songId);
            if ($song->getAlbum()->getId() !== $albumId || $song->getAlbum()->getArtist()->getId() !== $artistId) {
                throw $this->createNotFoundException('Mismatch between IDs provided in URL and song data.');
            }
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->render('song/song_details.html.twig', [
            'song' => $song,
            'album' => $song->getAlbum(),
            'artist' => $song->getAlbum()->getArtist(),
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

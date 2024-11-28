<?php

namespace App\Controller;

use App\Service\AlbumService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    private AlbumService $albumService;

    public function __construct(AlbumService $albumService)
    {
        $this->albumService = $albumService;
    }

    /**
     * Affiche la liste de tous les albums.
     *
     * @Route('/albums', name: 'album_list', methods: ['GET'])
     */

    #[Route('/albums', name: 'album_page', methods: ['GET'])]
    public function listAlbums(): Response
    {
        $albums = $this->albumService->getAllAlbums();

        if (empty($albums)) {
            throw $this->createNotFoundException('Aucun album trouvé.');
        }

        return $this->render('album/album.html.twig', [
            'albums' => $albums,
        ]);
    }

    #[Route('/artists/{artistId}/albums/{albumId}', name: 'albums_details', methods: ['GET'])]
    public function showAlbumDetails(int $artistId, int $albumId): Response
    {
        $album = $this->albumService->getAlbumDetails($albumId);

        if (!$album) {
            throw $this->createNotFoundException('Album non trouvé.');
        }

        return $this->render('album/album_details.html.twig', [
            'album' => $album,
            'artist' => $album->getArtist(),
            'songs' => $album->getSongs(),
        ]);
    }
}



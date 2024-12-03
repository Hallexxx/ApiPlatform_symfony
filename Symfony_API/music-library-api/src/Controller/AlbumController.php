<?php

namespace App\Controller;

use App\Service\AlbumService;
use App\Service\ArtistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlbumController extends AbstractController
{
    private AlbumService $albumService;

    public function __construct(AlbumService $albumService, ArtistService $artistService)
    {
        $this->albumService = $albumService;
        $this->artistService = $artistService;  
    }

    /**
     * Affiche la liste de tous les albums.
     *
     * @Route('/albums', name: 'album_list', methods: ['GET'])
     */

    #[Route('/albums', name: 'album_page', methods: ['GET'])]
    public function listAlbums(): Response
    {
        try {
            // Récupérer tous les albums avec leurs artistes associés
            $albums = $this->albumService->getAllAlbumsWithArtistsAndSongs();

            if (empty($albums)) {
                throw $this->createNotFoundException('Aucun album trouvé.');
            }

            // Organiser les albums par artiste avec leur index
            $albumsWithIndexes = [];
            foreach ($albums as $album) {
                $artistId = $album->getArtist()->getId();
                // Assurez-vous que l'index commence à 1 ou autre base selon le cas
                if (!isset($albumsWithIndexes[$artistId])) {
                    $albumsWithIndexes[$artistId] = [];
                }
                $albumsWithIndexes[$artistId][] = $album;
            }

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        // Envoyer les albums avec leurs index dans le template
        return $this->render('album/album.html.twig', [
            'albumsWithIndexes' => $albumsWithIndexes,
        ]);
    }

    #[Route('/artists/{artistId}/albums/{albumIndex}', name: 'albums_details', methods: ['GET'])]
    public function showAlbumDetails(int $artistId, int $albumIndex): Response
    {
        try {
            $artist = $this->artistService->getArtistWithAlbums($artistId);
            if (!$artist) {
                throw $this->createNotFoundException('Artist not found.');
            }
            $albums = $artist->getAlbums(); 
            if (!isset($albums[$albumIndex - 1])) {
                throw $this->createNotFoundException('Album not found at the specified index.');
            }

            $album = $albums[$albumIndex - 1];

        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->render('album/album_details.html.twig', [
            'album' => $album,
            'artist' => $artist,
            'songs' => $album->getSongs(),
            'albumIndex' => $albumIndex,
        ]);
    }

}



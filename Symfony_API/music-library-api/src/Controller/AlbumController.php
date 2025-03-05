<?php

namespace App\Controller;

use App\Service\AlbumService;
use App\Service\ArtistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AlbumController extends AbstractController
{
    private AlbumService $albumService;

    public function __construct(AlbumService $albumService, ArtistService $artistService)
    {
        $this->albumService = $albumService;
        $this->artistService = $artistService;  
    }

    #[Route('/albums', name: 'album_page', methods: ['GET'])]
    public function listAlbums(Request $request): Response
    {
        try {
            $search = $request->query->get('search', '');
            $date = $request->query->get('date', '');
            $artistName = $request->query->get('artist', '');

            $albums = $this->albumService->getAllAlbumsWithArtistsAndSongs();

            if (empty($albums)) {
                throw $this->createNotFoundException('Aucun album trouvé.');
            }

            $albumsWithIndexes = [];
            $artists = [];
            foreach ($albums as $album) {
                $artist = $album->getArtist();
                $artistId = $artist->getId();
                if (!in_array($artist, $artists, true)) {
                    $artists[] = $artist;
                }
                if (!isset($albumsWithIndexes[$artistId])) {
                    $albumsWithIndexes[$artistId] = [];
                }
                $albumsWithIndexes[$artistId][] = $album;
            }

            $user = $this->getUser();
            $favoritedAlbumIds = [];
            if ($user) {
                foreach ($user->getFavories() as $favoris) {
                    if ($favoris->getAlbum()) {
                        $favoritedAlbumIds[] = $favoris->getAlbum()->getId();
                    }
                }
            }
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->render('album/album.html.twig', [
            'albumsWithIndexes' => $albumsWithIndexes,
            'search'            => $search,
            'date'              => $date,
            'artist'            => $artistName,
            'artists'           => $artists,
            'selectedArtist'    => $artistName,
            'favoritedAlbumIds' => $favoritedAlbumIds,
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



<?php
// src/Controller/SearchController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ArtistRepository;
use App\Repository\AlbumRepository;
use App\Repository\SongRepository;

class SearchController extends AbstractController
{
    #[Route('/api/search', name: 'api_search', methods: ['GET'])]
    public function search(
        Request $request,
        ArtistRepository $artistRepository,
        AlbumRepository $albumRepository,
        SongRepository $songRepository
    ): JsonResponse {
        $query = $request->query->get('q');
        if (!$query) {
            return new JsonResponse([]);
        }
        
        $results = [];

        try {
            // Recherche dans les artistes en insensibilisant à la casse avec LOWER()
            $artists = $artistRepository->createQueryBuilder('a')
                ->where('LOWER(a.name) LIKE :query')
                ->setParameter('query', '%' . strtolower($query) . '%')
                ->getQuery()
                ->getResult();

            foreach ($artists as $artist) {
                $results[] = [
                    'label' => $artist->getName(),
                    'url'   => $this->generateUrl('artist_details', ['id' => $artist->getId()]),
                    'type'  => 'artist'
                ];
            }

            // Recherche dans les albums
            $albums = $albumRepository->createQueryBuilder('al')
                ->where('LOWER(al.title) LIKE :query')
                ->setParameter('query', '%' . strtolower($query) . '%')
                ->getQuery()
                ->getResult();

            foreach ($albums as $album) {
                $artist = $album->getArtist();
                $albumsOfArtist = $artist->getAlbums();
                $albumIndex = 1;
                foreach ($albumsOfArtist as $index => $al) {
                    if ($al->getId() === $album->getId()) {
                        $albumIndex = $index + 1;
                        break;
                    }
                }
                $results[] = [
                    'label' => $album->getTitle(),
                    'url'   => $this->generateUrl('albums_details', [
                        'artistId'   => $artist->getId(),
                        'albumIndex' => $albumIndex,
                    ]),
                    'type'  => 'album'
                ];
            }

            // Recherche dans les chansons
            $songs = $songRepository->createQueryBuilder('s')
                ->where('LOWER(s.title) LIKE :query')
                ->setParameter('query', '%' . strtolower($query) . '%')
                ->getQuery()
                ->getResult();

            foreach ($songs as $song) {
                $artist = $song->getAlbum()->getArtist();
                $albumsOfArtist = $artist->getAlbums();
                $albumIndex = 1;
                foreach ($albumsOfArtist as $index => $al) {
                    if ($al->getId() === $song->getAlbum()->getId()) {
                        $albumIndex = $index + 1;
                        break;
                    }
                }
                $songsOfAlbum = $song->getAlbum()->getSongs();
                $songIndex = 1;
                foreach ($songsOfAlbum as $index => $s) {
                    if ($s->getId() === $song->getId()) {
                        $songIndex = $index + 1;
                        break;
                    }
                }
                $results[] = [
                    'label' => $song->getTitle(),
                    'url'   => $this->generateUrl('song_details', [
                        'artistId'  => $artist->getId(),
                        'albumIndex'=> $albumIndex,
                        'songIndex' => $songIndex,
                    ]),
                    'type'  => 'song'
                ];
            }
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }

        return new JsonResponse($results);
    }
}

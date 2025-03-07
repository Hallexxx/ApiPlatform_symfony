<?php

namespace App\Controller;

use App\Service\AlbumService;
use App\Service\ArtistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Album;
use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AlbumController extends AbstractController
{
    private AlbumService $albumService;
    private EntityManagerInterface $entityManager;

    public function __construct(AlbumService $albumService, ArtistService $artistService, EntityManagerInterface $entityManager)
    {
        $this->albumService = $albumService;
        $this->artistService = $artistService;
        $this->entityManager = $entityManager;  
    }

    #[Route('/albums', name: 'album_page', methods: ['GET'])]
    public function listAlbums(Request $request): Response
    {
        try {
            $search = $request->query->get('search', '');
            $date = $request->query->get('date', '');
            $artistName = $request->query->get('artist', '');

            // Récupérer tous les albums (avec les artistes et les chansons associées)
            $albums = $this->albumService->getAllAlbumsWithArtistsAndSongs();

            if (empty($albums)) {
                throw $this->createNotFoundException('Aucun album trouvé.');
            }

            // Récupérer TOUS les artistes de la base de données
            $artists = $this->artistService->getAllArtists();

            // Organiser les albums par artiste
            $albumsWithIndexes = [];
            foreach ($albums as $album) {
                $artistId = $album->getArtist()->getId();
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
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        }

        return $this->render('album/album.html.twig', [
            'albumsWithIndexes' => $albumsWithIndexes,
            'search'            => $search,
            'date'              => $date,
            'artist'            => $artistName,
            'artists'           => $artists, // Liste complète des artistes
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

            $artists = $this->artistService->getAllArtists();
            
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
            'artists' => $artists,
            'songs' => $album->getSongs(),
            'albumIndex' => $albumIndex,
        ]);
    }

    #[Route('/album/add', name: 'album_add', methods: ['POST'])]
    public function addAlbum(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
           return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        $title    = $request->request->get('title');
        $dateStr  = $request->request->get('date');
        $artistId = $request->request->get('artist');

        if (!$title || !$dateStr || !$artistId) {
            return new Response('Champs requis manquants', Response::HTTP_BAD_REQUEST);
        }

        try {
            $date = new \DateTime($dateStr);
        } catch (\Exception $e) {
            return new Response('Format de date invalide', Response::HTTP_BAD_REQUEST);
        }

        $artist = $this->entityManager->getRepository(Artist::class)->find($artistId);
        if (!$artist) {
            return new Response('Artiste non trouvé', Response::HTTP_NOT_FOUND);
        }

        $album = new Album();
        $album->setTitle($title);
        $album->setDate($date);
        $album->setArtist($artist);
        $album->setCreatedBy($user);

        // Gestion de l'image
        $file = $request->files->get('image');
        if ($file instanceof UploadedFile) {
            $newFilename = uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter('media_directory'), $newFilename);
                $album->setImage($newFilename);
            } catch (FileException $e) {
                return new Response("Erreur lors de l'upload de l'image", Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $selectedImage = $request->request->get('image');
            if ($selectedImage) {
                $album->setImage($selectedImage);
            }
        }

        $this->entityManager->persist($album);
        $this->entityManager->flush();

        return new Response('success');
    }

    #[Route('/album/delete/{id}', name: 'album_delete', methods: ['POST'])]
    public function deleteAlbum(int $id): Response
    {
        $user = $this->getUser();
        $album = $this->entityManager->getRepository(Album::class)->find($id);
        if (!$album || $album->getCreatedBy() !== $user) {
            return new Response('Album non trouvé ou accès non autorisé', Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($album);
        $this->entityManager->flush();

        return new Response('success');
    }
}



<?php

namespace App\Controller;

use App\Entity\Song;
use App\Service\SongService;
use App\Service\ArtistService;
use App\Entity\Album;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


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

        // Récupérer tous les albums (ou filtrer selon vos besoins)
        $albums = $this->entityManager->getRepository(Album::class)->findAll();

        return $this->render('song/song.html.twig', [
            'songs'             => $songs,
            'artists'           => $artists,
            'favoritedSongIds'  => $favoritedSongIds,
            'isAuthenticated'   => $isAuthenticated,
            'albums'            => $albums, // On passe la variable albums
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
            'albums' => $albums,
            'artist' => $artist,
        ]);
    }

    #[Route('/song/add', name: 'song_add', methods: ['POST'])]
    public function addSong(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $song = new Song();
        $song->setTitle($request->request->get('title'));
        $song->setLyrics($request->request->get('lyrics'));
        try {
            $song->setDate(new \DateTime($request->request->get('date')));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Format de date invalide'], Response::HTTP_BAD_REQUEST);
        }
        $song->setLength((int)$request->request->get('length'));

        // Récupérer l'album choisi
        $albumId = $request->request->get('album');
        if (!$albumId) {
            return new JsonResponse(['error' => 'Veuillez sélectionner un album'], Response::HTTP_BAD_REQUEST);
        }
        $album = $this->entityManager->getRepository(Album::class)->find($albumId);
        if (!$album) {
            return new JsonResponse(['error' => 'Album non trouvé'], Response::HTTP_BAD_REQUEST);
        }
        $song->setAlbum($album);

        // Gestion de l'image
        if ($request->files->get('image')) {
            /** @var UploadedFile $file */
            $file = $request->files->get('image');
            $newFilename = uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->getParameter('media_directory'), $newFilename);
                $song->setImage($newFilename);
            } catch (FileException $e) {
                return new JsonResponse(['error' => 'Erreur lors de l’upload de l’image'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $selectedImage = $request->request->get('image');
            if ($selectedImage) {
                $song->setImage($selectedImage);
            }
        }

        // Gestion du mp3/mp4
        if ($request->files->get('mp3')) {
            /** @var UploadedFile $mp3File */
            $mp3File = $request->files->get('mp3');
            $newMp3Filename = uniqid() . '.' . $mp3File->guessExtension();
            try {
                $mp3File->move($this->getParameter('media_directory'), $newMp3Filename);
                $song->setFileUrl($newMp3Filename);
            } catch (FileException $e) {
                return new JsonResponse(['error' => 'Erreur lors de l’upload du mp3'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $selectedMp3 = $request->request->get('mp3');
            if ($selectedMp3) {
                $song->setFileUrl($selectedMp3);
            }
        }

        $song->setCreatedBy($user);

        $this->entityManager->persist($song);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
    
    #[Route('/song/delete/{id}', name: 'song_delete', methods: ['POST'])]
    public function deleteSong(int $id): JsonResponse
    {
        $user = $this->getUser();
        $song = $this->entityManager->getRepository(Song::class)->find($id);
        if (!$song || $song->getCreatedBy() !== $user) {
            return new JsonResponse(['error' => 'Song not found or unauthorized access'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($song);
        $this->entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}

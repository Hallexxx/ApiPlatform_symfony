<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SongRepository;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


class ProfileController extends AbstractController
{
    private SongRepository $songRepository;
    private AlbumRepository $albumRepository;
    private ArtistRepository $artistRepository;
    private EntityManagerInterface $entityManager;
    private string $mediaDirectory;

    public function __construct(
        SongRepository $songRepository,
        AlbumRepository $albumRepository,
        ArtistRepository $artistRepository,
        EntityManagerInterface $entityManager,
        string $mediaDirectory
    ) {
        $this->songRepository   = $songRepository;
        $this->albumRepository  = $albumRepository;
        $this->artistRepository = $artistRepository;
        $this->entityManager    = $entityManager;
        $this->mediaDirectory   = $mediaDirectory;
    }

    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Accès refusé.');
        }

        // Récupérer les créations de l'utilisateur via le champ "createdBy"
        $songs   = $this->songRepository->findBy(['createdBy' => $user]);
        $albums  = $this->albumRepository->findBy(['createdBy' => $user]);
        $artists = $this->artistRepository->findBy(['createdBy' => $user]);

        return $this->render('profile/profile.html.twig', [
            'user'    => $user,
            'songs'   => $songs,
            'albums'  => $albums,
            'artists' => $artists,
        ]);
    }

    #[Route('/profile/update', name: 'profile_update', methods: ['POST'])]
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $username = $request->request->get('username');
        $email = $request->request->get('email');

        if ($username) {
            $user->setUsername($username);
        }
        if ($email) {
            $user->setEmail($email);
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'success'  => true,
            'username' => $user->getUsername(),
            'email'    => $user->getEmail()
        ]);
    }

    #[Route('/profile/update/song/{id}', name: 'profile_update_song', methods: ['POST'])]
    public function updateSong(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser();
        $song = $this->songRepository->find($id);
        if (!$song || $song->getCreatedBy() !== $user) {
            return new JsonResponse(['error' => 'Chanson non trouvée ou accès non autorisé'], Response::HTTP_NOT_FOUND);
        }

        // Mise à jour du titre, paroles et date
        $title = $request->request->get('title');
        $lyrics = $request->request->get('lyrics');
        $date = $request->request->get('date');

        if ($title) {
            $song->setTitle($title);
        }
        if ($lyrics !== null) {
            $song->setLyrics($lyrics);
        }
        if ($date) {
            try {
                $song->setDate(new \DateTime($date));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Format de date invalide'], Response::HTTP_BAD_REQUEST);
            }
        }

        // Mise à jour de la durée (en secondes)
        $duration = $request->request->get('duration');
        if ($duration) {
            $song->setLengthInMinutes($duration);
        }

        // Mise à jour de l'album (id envoyé via le select)
        $albumId = $request->request->get('album');
        if ($albumId) {
            $album = $this->albumRepository->find($albumId);
            if ($album) {
                $song->setAlbum($album);
            } else {
                return new JsonResponse(['error' => 'Album non trouvé'], Response::HTTP_BAD_REQUEST);
            }
        }

        // Gestion de l'image du song
        /** @var UploadedFile $fileImage */
        $fileImage = $request->files->get('image');
        if ($fileImage instanceof UploadedFile) {
            $newFilename = uniqid().'.'.$fileImage->guessExtension();
            try {
                $fileImage->move($this->mediaDirectory, $newFilename);
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

        // Gestion du fichier mp3
        /** @var UploadedFile $fileMp3 */
        $fileMp3 = $request->files->get('file');
        if ($fileMp3 instanceof UploadedFile) {
            $newFilenameMp3 = uniqid().'.'.$fileMp3->guessExtension();
            try {
                $fileMp3->move($this->mediaDirectory, $newFilenameMp3);
                $song->setFileurl($newFilenameMp3);
            } catch (FileException $e) {
                return new JsonResponse(['error' => 'Erreur lors de l’upload du fichier mp3'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $selectedFile = $request->request->get('file');
            if ($selectedFile) {
                $song->setFileurl($selectedFile);
            }
        }

        $this->entityManager->flush();
        return new JsonResponse(['success' => true]);
    }


    #[Route('/profile/update/album/{id}', name: 'profile_update_album', methods: ['POST'])]
    public function updateAlbum(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser();
        $album = $this->albumRepository->find($id);
        if (!$album || $album->getCreatedBy() !== $user) {
            return new JsonResponse(['error' => 'Album non trouvé ou accès non autorisé'], Response::HTTP_NOT_FOUND);
        }
        
        // Mise à jour du titre
        $title = $request->request->get('title');
        if ($title) {
            $album->setTitle($title);
        }
        
        // Mise à jour de la date de sortie
        $dateStr = $request->request->get('date');
        if ($dateStr) {
            try {
                $album->setDate(new \DateTime($dateStr));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Format de date invalide'], Response::HTTP_BAD_REQUEST);
            }
        }
        
        // Gestion de l'image
        /** @var UploadedFile $file */
        $file = $request->files->get('image');
        if ($file instanceof UploadedFile) {
            $newFilename = uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->mediaDirectory, $newFilename);
                $album->setImage($newFilename);
            } catch (FileException $e) {
                return new JsonResponse(['error' => 'Erreur lors de l’upload du fichier'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $selectedImage = $request->request->get('image');
            if ($selectedImage) {
                $album->setImage($selectedImage); // Correction ici
            }
        }
        
        $this->entityManager->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/profile/update/artist/{id}', name: 'profile_update_artist', methods: ['POST'])]
    public function updateArtist(Request $request, int $id): JsonResponse
    {
        $user = $this->getUser();
        $artist = $this->artistRepository->find($id);
        if (!$artist || $artist->getCreatedBy() !== $user) {
            return new JsonResponse(['error' => 'Artiste non trouvé ou accès non autorisé'], Response::HTTP_NOT_FOUND);
        }
        
        // Mise à jour des textes
        $name = $request->request->get('name');
        if ($name) {
            $artist->setName($name);
        }
        $firstName = $request->request->get('first_name');
        if ($firstName !== null) {
            $artist->setFirstName($firstName);
        }
        $lastName = $request->request->get('last_name');
        if ($lastName !== null) {
            $artist->setLastName($lastName);
        }
        $birthDateStr = $request->request->get('birth_date');
        if ($birthDateStr) {
            try {
                $artist->setBirthDate(new \DateTime($birthDateStr));
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Format de date invalide'], Response::HTTP_BAD_REQUEST);
            }
        }
        $style = $request->request->get('style');
        if ($style !== null) {
            $artist->setStyle($style);
        }
        $nationality = $request->request->get('nationality');
        if ($nationality !== null) {
            $artist->setNationality($nationality);
        }
        
        // Gestion de l'image
        /** @var UploadedFile $file */
        $file = $request->files->get('image');
        if ($file instanceof UploadedFile) {
            $newFilename = uniqid() . '.' . $file->guessExtension();
            try {
                $file->move($this->mediaDirectory, $newFilename);
                $artist->setImage($newFilename);
            } catch (FileException $e) {
                return new JsonResponse(['error' => 'Erreur lors de l’upload du fichier'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $selectedImage = $request->request->get('image');
            if ($selectedImage) {
                $artist->setImage($selectedImage); // Correction ici
            }
        }
        
        $this->entityManager->flush();
        return new JsonResponse(['success' => true]);
    }

}

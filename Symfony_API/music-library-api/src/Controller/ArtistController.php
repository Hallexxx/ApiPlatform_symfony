<?php

namespace App\Controller;

use App\Service\ArtistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Custom endpoints for Artist.
 */
class ArtistController extends AbstractController
{
    private ArtistService $artistService;
    private EntityManagerInterface $entityManager;


    public function __construct(ArtistService $artistService, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->artistService = $artistService;
    }


    #[Route('/artists', name: 'artist_page', methods: ['GET'])]
    public function listArtists(): Response
    {
        $artists = $this->artistService->getAllArtists();

        // Calcul des favoris pour les artistes si l'utilisateur est connecté
        $favoritedArtistIds = [];
        $user = $this->getUser();
        if ($user) {
            foreach ($user->getFavories() as $favoris) {
                if ($favoris->getArtist()) {
                    $favoritedArtistIds[] = $favoris->getArtist()->getId();
                }
            }
        }

        // Extraction des valeurs distinctes pour les filtres
        $distinctStyles = [];
        $distinctNationalities = [];
        foreach ($artists as $artist) {
            $style = $artist->getStyle();
            $nationality = $artist->getNationality();
            if ($style && !in_array($style, $distinctStyles, true)) {
                $distinctStyles[] = $style;
            }
            if ($nationality && !in_array($nationality, $distinctNationalities, true)) {
                $distinctNationalities[] = $nationality;
            }
        }

        return $this->render('artist/artist.html.twig', [
            'artists'             => $artists,
            'favoritedArtistIds'  => $favoritedArtistIds,
            'distinctStyles'      => $distinctStyles,
            'distinctNationalities' => $distinctNationalities,
        ]);
    }

    #[Route('/artists/{id}', name: 'artist_details', methods: ['GET'])]
    public function showArtist(int $id): Response
    {
        $artist = $this->artistService->getArtistWithAlbumsAndSongs($id);

        return $this->render('artist/artist_details.html.twig', [
            'artist' => $artist,
        ]);
    }

    #[Route('/artist/add', name: 'artist_add', methods: ['POST'])]
    public function addArtist(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    
        $name = $request->request->get('name');
        $firstName = $request->request->get('first_name');
        $lastName = $request->request->get('last_name');
        $birthDateStr = $request->request->get('birth_date');
        $style = $request->request->get('style');
        $nationality = $request->request->get('nationality');
    
        if (!$name) {
            return new JsonResponse(['error' => 'Le nom de l\'artiste est obligatoire'], Response::HTTP_BAD_REQUEST);
        }
    
        $artist = new Artist();
        $artist->setName($name);
        $artist->setFirstName($firstName);
        $artist->setLastName($lastName);
        if ($birthDateStr) {
            try {
                $birthDate = new \DateTime($birthDateStr);
                $artist->setBirthDate($birthDate);
            } catch (\Exception $e) {
                return new JsonResponse(['error' => 'Format de date invalide'], Response::HTTP_BAD_REQUEST);
            }
        }
        $artist->setStyle($style);
        $artist->setNationality($nationality);
        $artist->setCreatedBy($user);
    
        if ($request->files->get('image')) {
            /** @var UploadedFile $file */
            $file = $request->files->get('image');
            $newFilename = $file->getClientOriginalName();
            try {
                $file->move($this->getParameter('media_directory'), $newFilename);
                $artist->setImage($newFilename);
            } catch (FileException $e) {
                return new JsonResponse(['error' => 'Erreur lors de l’upload de l’image'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $selectedImage = $request->request->get('image');
            if ($selectedImage) {
                $artist->setImage($selectedImage);
            }
        }
    
        $this->entityManager->persist($artist);
        $this->entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }
    #[Route('/artist/delete/{id}', name: 'artist_delete', methods: ['POST'])]
    public function deleteArtist(int $id): JsonResponse
    {
        $user = $this->getUser();
        $artist = $this->entityManager->getRepository(Artist::class)->find($id);
        if (!$artist || $artist->getCreatedBy() !== $user) {
            return new JsonResponse(['error' => 'Artiste non trouvé ou accès non autorisé'], Response::HTTP_NOT_FOUND);
        }
    
        $this->entityManager->remove($artist);
        $this->entityManager->flush();
    
        return new JsonResponse(['success' => true]);
    }

}

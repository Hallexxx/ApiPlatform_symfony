<?php

namespace App\Controller;

use App\Entity\Favoris;
use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FavorisController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/favoris', name: 'favoris_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $favoris = $user->getFavories();
        return $this->render('favoris/favoris.html.twig', [
            'favoris' => $favoris,
        ]);
    }

    #[Route('/favoris/add', name: 'favoris_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $type = $request->request->get('type'); // album, artist, or song
        $id = $request->request->get('id');

        $favoris = new Favoris();
        $favoris->setUser($user);

        switch ($type) {
            case 'album':
                $album = $this->entityManager->getRepository(Album::class)->find($id);
                if (!$album) {
                    return $this->json(['error' => 'Album not found'], 404);
                }
                $favoris->setAlbum($album);
                break;
            case 'artist':
                $artist = $this->entityManager->getRepository(Artist::class)->find($id);
                if (!$artist) {
                    return $this->json(['error' => 'Artist not found'], 404);
                }
                $favoris->setArtist($artist);
                break;
            case 'song':
                $song = $this->entityManager->getRepository(Song::class)->find($id);
                if (!$song) {
                    return $this->json(['error' => 'Song not found'], 404);
                }
                $favoris->setSong($song);
                break;
            default:
                return $this->json(['error' => 'Invalid type'], 400);
        }

        $this->entityManager->persist($favoris);
        $this->entityManager->flush();

        return $this->json(['success' => 'Favoris added'], 201);
    }

    #[Route('/favoris/remove', name: 'favoris_remove', methods: ['POST'])]
    public function remove(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $id = $request->request->get('id');
        $favoris = $this->entityManager->getRepository(Favoris::class)->find($id);

        if (!$favoris || $favoris->getUser() !== $user) {
            return $this->json(['error' => 'Favoris not found or not authorized'], 404);
        }

        $this->entityManager->remove($favoris);
        $this->entityManager->flush();

        return $this->json(['success' => 'Favoris removed'], 200);
    }
}

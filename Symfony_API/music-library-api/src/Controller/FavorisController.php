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

        $favorites = $user->getFavories();
        $favoritesArray = [];
        foreach ($favorites as $fav) {
            $item = [
                'id'     => $fav->getId(),
                'song'   => $fav->getSong(),    
                'album'  => $fav->getAlbum(),   
                'artist' => $fav->getArtist(),  
            ];
            $favoritesArray[] = $item;
        }

        return $this->render('favoris/favoris.html.twig', [
            'favoris' => $favoritesArray,
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

    #[Route('/favoris/toggle', name: 'favoris_toggle', methods: ['POST'])]
    public function toggleFavoris(Request $request): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        
        $type = $request->request->get('type');                                                                                                                                                                                                              
        $entityId = $request->request->get('id');
        
        $favorisRepo = $this->entityManager->getRepository(Favoris::class);
        $existingFavoris = $favorisRepo->findOneBy([
            'user' => $user,
            $type => $entityId, 
        ]);
        
        if ($existingFavoris) {
            $this->entityManager->remove($existingFavoris);
            $this->entityManager->flush();
            return new JsonResponse(['favorited' => false]);
        } else {
            switch ($type) {
                case 'song':
                    $entity = $this->entityManager->getRepository(Song::class)->find($entityId);
                    break;
                case 'artist':
                    $entity = $this->entityManager->getRepository(Artist::class)->find($entityId);
                    break;
                case 'album':
                    $entity = $this->entityManager->getRepository(Album::class)->find($entityId);
                    break;
                default:
                    return new JsonResponse(['error' => 'Invalid type'], Response::HTTP_BAD_REQUEST);
            }
            
            if (!$entity) {
                return new JsonResponse(['error' => ucfirst($type) . ' not found'], Response::HTTP_NOT_FOUND);
            }
            
            $favoris = new Favoris();
            $favoris->setUser($user);
            if ($type === 'song') {
                $favoris->setSong($entity);
            } elseif ($type === 'artist') {
                $favoris->setArtist($entity);
            } elseif ($type === 'album') {
                $favoris->setAlbum($entity);
            }
            
            $this->entityManager->persist($favoris);
            $this->entityManager->flush();
            return new JsonResponse(['favorited' => true]);
        }
    }

}

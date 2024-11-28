<?php

namespace App\Controller;

use App\Service\ArtistService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Custom endpoints for Artist.
 */
class ArtistController extends AbstractController
{
    private ArtistService $artistService;

    public function __construct(ArtistService $artistService)
    {
        $this->artistService = $artistService;
    }

    #[Route('/artists', name: 'artist_page', methods: ['GET'])]
    public function listArtists(): Response
    {
        $artists = $this->artistService->getAllArtists();

        return $this->render('artist/artist.html.twig', [
            'artists' => $artists,
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

}

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

}

<?php

namespace App\Service;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;

class AlbumService
{
    private AlbumRepository $albumRepository;

    public function __construct(AlbumRepository $albumRepository)
    {
        $this->albumRepository = $albumRepository;
    }

    /**
     * Récupère tous les albums.
     */
    public function getAllAlbums(): array
    {
        return $this->albumRepository->findAll();  // Utilisation du repository pour récupérer les albums
    }

    /**
     * Crée un album.
     */
    public function createAlbum(string $title, \DateTimeInterface $date, Artist $artist): Album
    {
        $album = new Album();
        $album->setTitle($title)
              ->setDate($date)
              ->setArtist($artist);

        // Persiste l'album
        $this->albumRepository->save($album); // Sauvegarde dans la base de données via le repository
        return $album;
    }

    public function getAllAlbumsWithArtistsAndSongs(): array
    {
        return $this->albumRepository->findAllWithArtistsAndSongs();
    }


    /**
     * Récupère un album avec ses chansons et son artiste.
     */
    public function getAlbumDetails(int $id): ?Album
    {
        return $this->albumRepository->findWithSongsAndArtist($id);  // Méthode spécifique pour récupérer album avec ses chansons
    }
}

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

    public function getFilteredAlbums(string $search = '', string $date = '', string $artistName = ''): array
    {
        $qb = $this->albumRepository->createQueryBuilder('a')
            ->join('a.artist', 'artist')
            ->where('a.title LIKE :search')
            ->setParameter('search', '%' . $search . '%');

        if ($date) {
            $qb->andWhere('a.date = :date')
            ->setParameter('date', $date);
        }

        if ($artistName) {
            $qb->andWhere('artist.name LIKE :artistName')
            ->setParameter('artistName', '%' . trim($artistName) . '%');  // Ajout de trim pour éviter les espaces superflus
        }

        return $qb->getQuery()->getResult();
    }


    /**
     * Récupère un album avec ses chansons et son artiste.
     */
    public function getAlbumDetails(int $id): ?Album
    {
        return $this->albumRepository->findWithSongsAndArtist($id);  // Méthode spécifique pour récupérer album avec ses chansons
    }
}

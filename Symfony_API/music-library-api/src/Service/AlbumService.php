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

    public function getAllAlbums(): array
    {
        return $this->albumRepository->findAll();  
    }

    public function createAlbum(string $title, \DateTimeInterface $date, Artist $artist): Album
    {
        $album = new Album();
        $album->setTitle($title)
              ->setDate($date)
              ->setArtist($artist);

        $this->albumRepository->save($album); 
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
            ->setParameter('artistName', '%' . trim($artistName) . '%');  
        }

        return $qb->getQuery()->getResult();
    }

    public function getAlbumDetails(int $id): ?Album
    {
        return $this->albumRepository->findWithSongsAndArtist($id);  
    }
}

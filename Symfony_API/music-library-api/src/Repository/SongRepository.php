<?php

namespace App\Repository;

use App\Entity\Song;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Song>
 */
class SongRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Song::class);
    }

    /**
     * @return Song[] Returns an array of Song objects with album and artist data
     */

    public function findAllWithAlbumAndArtist(): array
    {
        return $this->createQueryBuilder('s')
        ->leftJoin('s.album', 'a') // Jointure avec l'album
        ->leftJoin('a.artist', 'ar') // Jointure avec l'artiste de l'album
        ->addSelect('a', 'ar') // Inclure les données de l'album et de l'artiste
        ->getQuery()
        ->getResult();
    }

    public function findOneWithAlbumAndArtist(int $id): ?Song
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.album', 'a') // Jointure avec l'album
            ->leftJoin('a.artist', 'ar') // Jointure avec l'artiste
            ->addSelect('a', 'ar') // Inclure les données de l'album et de l'artiste
            ->where('s.id = :id') // Filtrer par ID de la chanson
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(); // Retourne une chanson ou null
    }
}

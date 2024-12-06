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
        ->leftJoin('s.album', 'a')
        ->leftJoin('a.artist', 'ar') 
        ->addSelect('a', 'ar') 
        ->getQuery()
        ->getResult();
    }

    public function findOneWithAlbumAndArtist(int $id): ?Song
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.album', 'a') 
            ->leftJoin('a.artist', 'ar') 
            ->addSelect('a', 'ar') 
            ->where('s.id = :id') 
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(); 
    }
}

<?php

namespace App\Repository;

use App\Entity\Album;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AlbumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Album::class);
    }

    public function findAllWithArtistsAndSongs(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.artist', 'ar') 
            ->addSelect('ar')  
            ->leftJoin('a.songs', 's')  
            ->addSelect('s')  
            ->getQuery()
            ->getResult();
    }

    
    public function findWithSongsAndArtist(int $id): ?Album
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.artist', 'ar') 
            ->addSelect('ar')
            ->leftJoin('a.songs', 's')  
            ->addSelect('s')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();  
    }

    public function findLimitedAlbums(int $limit = 10): array
    {
        $albums = $this->createQueryBuilder('a')
            ->leftJoin('a.artist', 'ar')
            ->addSelect('ar')
            ->leftJoin('a.songs', 's')
            ->addSelect('s')
            ->getQuery()
            ->getResult();

        shuffle($albums);
        return array_slice($albums, 0, $limit);
    }
}

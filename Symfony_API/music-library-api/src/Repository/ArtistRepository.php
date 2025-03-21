<?php

namespace App\Repository;

use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artist>
 */
class ArtistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    public function findAllWithAlbums(): array
    {
        return $this->createQueryBuilder('ar')
            ->leftJoin('ar.albums', 'a')
            ->addSelect('a')
            ->getQuery()
            ->getResult();
    }

}

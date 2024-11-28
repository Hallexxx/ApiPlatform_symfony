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

    /**
     * Récupère tous les albums avec leurs artistes et chansons associés.
     */
    public function findAllWithArtistsAndSongs(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.artist', 'ar')  // On fait la jointure avec l'artiste
            ->addSelect('ar')  // On sélectionne aussi l'artiste
            ->leftJoin('a.songs', 's')  // On joint les chansons de l'album
            ->addSelect('s')  // On sélectionne aussi les chansons
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère un album spécifique avec son artiste et ses chansons.
     */
    public function findWithSongsAndArtist(int $id): ?Album
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.artist', 'ar')  // Jointure avec l'artiste
            ->addSelect('ar')
            ->leftJoin('a.songs', 's')  // Jointure avec les chansons
            ->addSelect('s')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();  // Retourne un seul album ou null si pas trouvé
    }
}

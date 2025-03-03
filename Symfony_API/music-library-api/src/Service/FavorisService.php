<?php

namespace App\Service;

use App\Entity\Favoris;
use Doctrine\ORM\EntityManagerInterface;

class FavorisService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addFavoris($user, $type, $entity): Favoris
    {
        $favoris = new Favoris();
        $favoris->setUser($user);

        if ($type === 'album') {
            $favoris->setAlbum($entity);
        } elseif ($type === 'artist') {
            $favoris->setArtist($entity);
        } elseif ($type === 'song') {
            $favoris->setSong($entity);
        }

        $this->entityManager->persist($favoris);
        $this->entityManager->flush();

        return $favoris;
    }
}

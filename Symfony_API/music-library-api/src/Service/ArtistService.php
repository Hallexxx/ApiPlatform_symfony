<?php

namespace App\Service;

use App\Entity\Artist;
use App\Repository\ArtistRepository;

class ArtistService
{
    private ArtistRepository $artistRepository;

    public function __construct(ArtistRepository $artistRepository)
    {
        $this->artistRepository = $artistRepository;
    }

    public function getAllArtists(): array
    {
        $artists = $this->artistRepository->findAll();
        return $artists;
    }

    public function getArtistWithAlbumsAndSongs(int $artistId): ?Artist
    {
        $artist = $this->artistRepository->createQueryBuilder('a')
            ->leftJoin('a.albums', 'al')
            ->leftJoin('al.songs', 's')
            ->addSelect('al', 's')
            ->where('a.id = :id')
            ->setParameter('id', $artistId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$artist) {
            throw new \Exception("Artist not found");
        }

        return $artist;
    }

    public function getArtistWithAlbums(int $artistId): ?Artist
    {
        $artist = $this->artistRepository->find($artistId);
        if (!$artist) {
            throw new \Exception("Artist not found");
        }
        return $artist;
    }

    public function deleteArtist(int $artistId): void
    {
        $artist = $this->artistRepository->find($artistId);
        if (!$artist) {
            throw new \Exception("Artist not found");
        }
        $this->artistRepository->remove($artist, true);
    }
}

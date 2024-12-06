<?php

namespace App\Service;

use App\Repository\SongRepository;
use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;

class MusicLibraryService
{
    private $songRepository;
    private $albumRepository;
    private $artistRepository;

    public function __construct(SongRepository $songRepository, AlbumRepository $albumRepository, ArtistRepository $artistRepository)
    {
        $this->songRepository = $songRepository;
        $this->albumRepository = $albumRepository;
        $this->artistRepository = $artistRepository;
    }

    public function getAllSongs(): array
    {
        $songs = $this->songRepository->findAllWithAlbumAndArtist();
        usort($songs, fn($a, $b) => strcmp($a->getTitle(), $b->getTitle()));
        return $songs;
    }


    public function getAllAlbums(): array
    {
        $albums = $this->albumRepository->findAll();
        return $albums;
    }

    public function getAllArtists(): array
    {
        $artists = $this->artistRepository->findAll();
        return $artists;
    }
}

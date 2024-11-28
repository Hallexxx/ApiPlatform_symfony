<?php

namespace App\Service;

use App\Repository\SongRepository;
use App\Entity\Song;
use App\Exception\SongNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class SongService
{
    private SongRepository $songRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(SongRepository $songRepository, EntityManagerInterface $entityManager)
    {
        $this->songRepository = $songRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Retrieve a song by its ID.
     *
     * @param int $songId
     * @return Song
     * @throws SongNotFoundException
     */
    public function getSongById(int $songId): Song
    {
        $song = $this->songRepository->findOneWithAlbumAndArtist($songId);

        if (!$song) {
            throw new SongNotFoundException(sprintf('Song with ID %d not found.', $songId));
        }

        return $song;
    }

    public function getAllSongs(): array
    {
        $songs = $this->songRepository->findAllWithAlbumAndArtist();
        usort($songs, fn($a, $b) => strcmp($a->getTitle(), $b->getTitle()));
        return $songs;
    }
    
    /**
     * Create a new song.
     *
     * @param array $data
     * @return Song
     */
    public function createSong(array $data): Song
    {
        $song = new Song();

        $song->setTitle($data['title'])
            ->setLength($data['length'])
            ->setFileUrl($data['fileUrl'] ?? null)
            ->setGenre($data['genre'] ?? null)
            ->setReleaseDate($data['releaseDate'] ?? null);

        if (isset($data['album'])) {
            $song->setAlbum($data['album']);
        }

        $this->entityManager->persist($song);
        $this->entityManager->flush();

        return $song;
    }

    /**
     * Update an existing song.
     *
     * @param int $songId
     * @param array $data
     * @return Song
     * @throws SongNotFoundException
     */
    public function updateSong(int $songId, array $data): Song
    {
        $song = $this->getSongById($songId);

        if (isset($data['title'])) {
            $song->setTitle($data['title']);
        }
        if (isset($data['length'])) {
            $song->setLength($data['length']);
        }
        if (isset($data['fileUrl'])) {
            $song->setFileUrl($data['fileUrl']);
        }
        if (isset($data['genre'])) {
            $song->setGenre($data['genre']);
        }
        if (isset($data['releaseDate']) && $data['releaseDate'] instanceof \DateTimeInterface) {
            $song->setReleaseDate($data['releaseDate']);
        }

        $this->entityManager->flush();

        return $song;
    }

    /**
     * Delete a song by its ID.
     *
     * @param int $songId
     * @throws SongNotFoundException
     */
    public function deleteSong(int $songId): void
    {
        $song = $this->getSongById($songId);

        $this->entityManager->remove($song);
        $this->entityManager->flush();
    }
}

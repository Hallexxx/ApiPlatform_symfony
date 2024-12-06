<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\CollectionOperations;
use ApiPlatform\Metadata\GetCollection;
/**
 * Represents a song in the music library.
 */
#[ORM\Entity(repositoryClass: SongRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['song:read']],
    denormalizationContext: ['groups' => ['song:write']],
    operations: [
        new Get(
            uriTemplate: '/artists/{artistId}/albums/{albumIndex}/songs/{songIndex}',
            name: 'get_song'
        ),
        new GetCollection(
            uriTemplate: '/songs',
            name: 'get_songs'
        ),
        new Post(
            uriTemplate: '/artists/{artistId}/albums/{albumIndex}/songs',
            name: 'create_song'
        ),
        new Put(
            uriTemplate: '/artists/{artistId}/albums/{albumIndex}/songs/{songIndex}',
            name: 'update_song'
        ),
        new Delete(
            uriTemplate: '/artists/{artistId}/albums/{albumIndex}/songs/{songIndex}',
            name: 'delete_song'
        ),
    ],
    filters: ['song.duration', 'song.artist', 'song.name']
)]
#[ApiFilter(RangeFilter::class, properties: ['duration'])]
#[ApiFilter(SearchFilter::class, properties: ['artist' => 'exact', 'name' => 'partial'])]
class Song
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['song:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['song:read', 'song:write'])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['song:read', 'song:write'])]
    private ?int $length = null;

    #[ORM\ManyToOne(targetEntity: Album::class, inversedBy: 'songs')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    #[Groups(['song:read', 'song:write'])]
    private ?Album $album = null;

    #[ORM\Column(type: 'date')]
    #[Groups(['song:read', 'song:write'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['song:read', 'song:write'])]
    private ?string $fileUrl = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['song:read', 'song:write'])]
    private ?string $image = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(['song:read', 'song:write'])]
    private ?string $lyrics = null;

    // Getters et setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): static
    {
        $this->length = $length;
        return $this;
    }

    public function getLengthInMinutes(): ?string
    {
        if ($this->length === null) { 
            return null;
        }

        $minutes = floor($this->length / 60);
        $seconds = $this->length % 60;       

        return sprintf('%d:%02d', $minutes, $seconds); 
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): static
    {
        $this->album = $album;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;
        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(?string $fileUrl): static
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getLyrics(): ?string
    {
        return $this->lyrics;
    }

    public function setLyrics(?string $lyrics): static
    {
        $this->lyrics = $lyrics;
        return $this;
    }
}

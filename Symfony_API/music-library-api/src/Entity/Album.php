<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Represents a music album.
 */
#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ApiResource] // Expose l'entité via l'API.
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial', 'date' => 'exact'])]
class Album
{
    /**
     * The unique identifier of the album.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['song:read', 'album:read'])]
    private ?int $id = null;

    /**
     * The title of the album.
     */
    #[ORM\Column(length: 255)]
    #[Groups(['song:read', 'album:read'])]
    private ?string $title = null;

    /**
     * The release date of the album.
     */
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['album:read'])]
    private ?\DateTimeInterface $date = null;

    /**
     * The artist who created the album.
     */
    #[ORM\ManyToOne(targetEntity: Artist::class, inversedBy: 'albums')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['album:read'])]
    private ?Artist $artist = null;

    /**
     * The image of the album.
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['album:read'])]
    private ?string $image = null;

    /**
     * @var Collection<int, Song> The songs in this album.
     */
    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Song::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['album:read'])]
    private Collection $songs;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->songs = new ArrayCollection();
    }

    // === Getters and Setters ===

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): static
    {
        $this->artist = $artist;

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

    /**
     * Get all songs in this album.
     * 
     * @return Collection<int, Song>
     */
    public function getSongs(): Collection
    {
        return $this->songs;
    }

    /**
     * Add a song to the album.
     */
    public function addSong(Song $song): static
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
            $song->setAlbum($this);
        }

        return $this;
    }

    /**
     * Remove a song from the album.
     */
    public function removeSong(Song $song): static
    {
        if ($this->songs->removeElement($song)) {
            if ($song->getAlbum() === $this) {
                $song->setAlbum(null);
            }
        }

        return $this;
    }
}

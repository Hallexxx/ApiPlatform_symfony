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
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;


#[ORM\Entity(repositoryClass: AlbumRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['album:read']],
    denormalizationContext: ['groups' => ['album:write']],
    operations: [
        new Get(
            uriTemplate: '/artists/{artistId}/albums/{albumIndex}',
            name: 'get_album'
        ),
        new GetCollection(
            uriTemplate: '/albums',
            name: 'get_albums'
        ),
        new Post(
            uriTemplate: '/artists/{artistId}/albums',
            name: 'create_album'
        ),
        new Put(
            uriTemplate: '/artists/{artistId}/albums/{albumIndex}',
            name: 'update_album'
        ),
        new Delete(
            uriTemplate: '/artists/{artistId}/albums/{albumIndex}',
            name: 'delete_album'
        ),
    ]
)]


#[ApiFilter(RangeFilter::class, properties: ['date'])] // Filtrage par plage de dates

class Album
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['song:read', 'album:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['song:read', 'album:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['album:read'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(targetEntity: Artist::class, inversedBy: 'albums')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['album:read', 'song:read'])]
    #[MaxDepth(1)]
    private ?Artist $artist = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['album:read'])]
    private ?string $image = null;

    /**
     * @var Collection<int, Song> The songs in this album.
     */
    #[ORM\OneToMany(mappedBy: 'album', targetEntity: Song::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['album:read'])]
    private Collection $songs;

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

    public function addSong(Song $song): static
    {
        if (!$this->songs->contains($song)) {
            $this->songs->add($song);
            $song->setAlbum($this);
        }

        return $this;
    }

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

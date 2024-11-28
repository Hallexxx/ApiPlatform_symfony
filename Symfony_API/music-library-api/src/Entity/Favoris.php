<?php

namespace App\Entity;

use App\Repository\FavorisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: FavorisRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['favoris:read']],
    denormalizationContext: ['groups' => ['favoris:write']],
    security: "is_granted('ROLE_USER')",
    securityMessage: "Access denied."
)]
class Favoris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['favoris:read', 'user:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'favories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['favoris:read', 'favoris:write'])]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Album::class)]
    #[Groups(['favoris:read', 'favoris:write'])]
    private ?Album $album = null;

    #[ORM\ManyToOne(targetEntity: Artist::class)]
    #[Groups(['favoris:read', 'favoris:write'])]
    private ?Artist $artist = null;

    #[ORM\ManyToOne(targetEntity: Song::class)]
    #[Groups(['favoris:read', 'favoris:write'])]
    private ?Song $song = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAlbum(): ?Album
    {
        return $this->album;
    }

    public function setAlbum(?Album $album): self
    {
        $this->album = $album;

        return $this;
    }

    public function getArtist(): ?Artist
    {
        return $this->artist;
    }

    public function setArtist(?Artist $artist): self
    {
        $this->artist = $artist;

        return $this;
    }

    public function getSong(): ?Song
    {
        return $this->song;
    }

    public function setSong(?Song $song): self
    {
        $this->song = $song;

        return $this;
    }
}

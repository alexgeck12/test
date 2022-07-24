<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
	#[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
	#[Assert\NotBlank]
    private ?string $summary = null;

    #[ORM\Column(length: 255)]
	#[Assert\NotBlank]
    private ?string $mpaa_rating = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
	#[Assert\DateTime]
    private ?\DateTimeInterface $publication_date = null;

    #[ORM\ManyToOne(inversedBy: 'review')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Movie $movie = null;

    #[ORM\ManyToOne(inversedBy: 'review')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Critic $critic = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getMpaaRating(): ?string
    {
        return $this->mpaa_rating;
    }

    public function setMpaaRating(string $mpaa_rating): self
    {
        $this->mpaa_rating = $mpaa_rating;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publication_date;
    }

    public function setPublicationDate(\DateTimeInterface $publication_date): self
    {
        $this->publication_date = $publication_date;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getCritic(): ?Critic
    {
        return $this->critic;
    }

    public function setCritic(?Critic $critic): self
    {
        $this->critic = $critic;

        return $this;
    }
}

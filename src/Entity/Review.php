<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tourist')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tour $tour = null;

    #[ORM\ManyToOne(inversedBy: 'rating')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tourist $tourist = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $review_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTour(): ?Tour
    {
        return $this->tour;
    }

    public function setTour(?Tour $tour): static
    {
        $this->tour = $tour;

        return $this;
    }

    public function getTourist(): ?Tourist
    {
        return $this->tourist;
    }

    public function setTourist(?Tourist $tourist): static
    {
        $this->tourist = $tourist;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getReviewDate(): ?\DateTimeInterface
    {
        return $this->review_date;
    }

    public function setReviewDate(\DateTimeInterface $review_date): static
    {
        $this->review_date = $review_date;

        return $this;
    }
}

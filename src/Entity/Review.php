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
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Tour is required')]
    private ?Tour $tour = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Tourist is required')]
    private ?Tourist $tourist = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Rating is required')]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: 'Rating must be between {{ min }} and {{ max }}'
    )]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull(message: 'Comment is required')]
    #[Assert\Length(
        max: 5000,
        maxMessage: 'Comment cannot exceed {{ limit }} characters'
    )]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Review date is required')]
    #[Assert\Type(
        type: \DateTimeInterface::class,
        message: 'Invalid date format'
    )]
    #[Assert\LessThanOrEqual('now', message: 'Review date cannot be in the future')]
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
        if ($rating > 10) {
            throw new \InvalidArgumentException('Rating cannot be greater than 10.');
        }

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


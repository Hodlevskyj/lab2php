<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['review:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['review:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['review:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['review:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['review:read:collection', 'review:read:item'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Tour is required')]
    #[Groups(['review:read:collection', 'review:read:item', 'review:write'])]
    private ?Tour $tour = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Tourist is required')]
    #[Groups(['review:read:collection', 'review:read:item', 'review:write'])]
    private ?Tourist $tourist = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Rating is required')]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: 'Rating must be between {{ min }} and {{ max }}'
    )]
    #[Groups(['review:read:collection', 'review:read:item', 'review:write'])]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull(message: 'Comment is required')]
    #[Assert\Length(
        max: 5000,
        maxMessage: 'Comment cannot exceed {{ limit }} characters'
    )]
    #[Groups(['review:read:item', 'review:write'])]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Review date is required')]
    #[Assert\Type(
        type: \DateTimeInterface::class,
        message: 'Invalid date format'
    )]
    #[Assert\LessThanOrEqual('now', message: 'Review date cannot be in the future')]
    #[Groups(['review:read:collection', 'review:read:item', 'review:write'])]
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

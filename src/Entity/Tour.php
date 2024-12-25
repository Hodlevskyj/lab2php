<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\TourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['tour:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['tour:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['tour:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['tour:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: TourRepository::class)]
class Tour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tour:read:collection', 'tour:read:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'The tour name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'The tour name cannot be longer than {{ limit }} characters'
    )]
    #[Groups(['tour:read:collection', 'tour:read:item', 'tour:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'The description is required')]
    #[Assert\Length(
        max: 5000,
        maxMessage: 'The description cannot exceed {{ limit }} characters'
    )]
    #[Groups(['tour:read:item', 'tour:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'The duration is required')]
    #[Assert\Positive(message: 'The duration must be a positive number')]
    #[Groups(['tour:read:collection', 'tour:read:item', 'tour:write'])]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Assert\NotBlank(message: 'The price is required')]
    #[Assert\Positive(message: 'The price must be greater than zero')]
    #[Groups(['tour:read:collection', 'tour:read:item', 'tour:write'])]
    private ?string $price = null;

    #[ORM\OneToMany(mappedBy: 'tour', targetEntity: TourGuide::class)]
    #[Groups(['tour:read:item'])]
    private Collection $tourGuides;

    #[ORM\OneToMany(mappedBy: 'tour', targetEntity: Booking::class)]
    #[Groups(['tour:read:item'])]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'tour', targetEntity: Review::class)]
    #[Groups(['tour:read:item'])]
    private Collection $reviews;

    public function __construct()
    {
        $this->tourGuides = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, TourGuide>
     */
    public function getTourGuides(): Collection
    {
        return $this->tourGuides;
    }

    public function addTourGuide(TourGuide $tourGuide): static
    {
        if (!$this->tourGuides->contains($tourGuide)) {
            $this->tourGuides->add($tourGuide);
            $tourGuide->setTour($this);
        }

        return $this;
    }

    public function removeTourGuide(TourGuide $tourGuide): static
    {
        if ($this->tourGuides->removeElement($tourGuide)) {
            if ($tourGuide->getTour() === $this) {
                $tourGuide->setTour(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setTour($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getTour() === $this) {
                $booking->setTour(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setTour($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getTour() === $this) {
                $review->setTour(null);
            }
        }

        return $this;
    }
}

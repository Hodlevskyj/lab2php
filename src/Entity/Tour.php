<?php

namespace App\Entity;

use App\Repository\TourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TourRepository::class)]
class Tour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'The tour name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'The tour name cannot be longer than {{ limit }} characters'
    )]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'The description is required')]
    #[Assert\Length(
        max: 5000,
        maxMessage: 'The description cannot exceed {{ limit }} characters'
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'The duration is required')]
    #[Assert\Positive(message: 'The duration must be a positive number')]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Assert\NotBlank(message: 'The price is required')]
    #[Assert\Positive(message: 'The price must be greater than zero')]
    private ?string $price = null;

    /**
     * @var Collection<int, TourGuide>
     */
    #[ORM\OneToMany(targetEntity: TourGuide::class, mappedBy: 'tour')]
    private Collection $tourGuides;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'tour')]
    private Collection $bookings;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'tour')]
    private Collection $tourist;

    public function __construct()
    {
        $this->tourGuides = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->tourist = new ArrayCollection();
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
            // set the owning side to null (unless already changed)
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
            // set the owning side to null (unless already changed)
            if ($booking->getTour() === $this) {
                $booking->setTour(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getTourist(): Collection
    {
        return $this->tourist;
    }

    public function addTourist(Review $tourist): static
    {
        if (!$this->tourist->contains($tourist)) {
            $this->tourist->add($tourist);
            $tourist->setTour($this);
        }

        return $this;
    }

    public function removeTourist(Review $tourist): static
    {
        if ($this->tourist->removeElement($tourist)) {
            // set the owning side to null (unless already changed)
            if ($tourist->getTour() === $this) {
                $tourist->setTour(null);
            }
        }

        return $this;
    }
}

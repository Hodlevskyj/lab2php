<?php

namespace App\Entity;

use App\Repository\TourRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TourRepository::class)]
class Tour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
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

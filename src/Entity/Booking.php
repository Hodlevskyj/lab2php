<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tourist $tourist = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tour $tour = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $booking_date = null;

    #[ORM\Column]
    private ?int $number_of_people = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total_price = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'booking')]
    private Collection $amount;

    public function __construct()
    {
        $this->amount = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTour(): ?Tour
    {
        return $this->tour;
    }

    public function setTour(?Tour $tour): static
    {
        $this->tour = $tour;

        return $this;
    }

    public function getBookingDate(): ?\DateTimeInterface
    {
        return $this->booking_date;
    }

    public function setBookingDate(\DateTimeInterface $booking_date): static
    {
        $this->booking_date = $booking_date;

        return $this;
    }

    public function getNumberOfPeople(): ?int
    {
        return $this->number_of_people;
    }

    public function setNumberOfPeople(int $number_of_people): static
    {
        $this->number_of_people = $number_of_people;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->total_price;
    }

    public function setTotalPrice(string $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getAmount(): Collection
    {
        return $this->amount;
    }

    public function addAmount(Payment $amount): static
    {
        if (!$this->amount->contains($amount)) {
            $this->amount->add($amount);
            $amount->setBooking($this);
        }

        return $this;
    }

    public function removeAmount(Payment $amount): static
    {
        if ($this->amount->removeElement($amount)) {
            // set the owning side to null (unless already changed)
            if ($amount->getBooking() === $this) {
                $amount->setBooking(null);
            }
        }

        return $this;
    }
}

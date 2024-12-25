<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\BookingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['booking:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['booking:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['booking:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['booking:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Tourist must not be null')]
    #[Groups(['booking:read:item', 'booking:write'])]
    private ?Tourist $tourist = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Tour must not be null')]
    #[Groups(['booking:read:item', 'booking:write'])]
    private ?Tour $tour = null;

    #[ORM\Column(type: 'datetime_mutable')]
    #[Assert\NotBlank(message: 'Booking date is required')]
    #[Assert\Type(\DateTimeInterface::class, message: 'Invalid date format')]
    #[Assert\GreaterThan('today', message: 'Booking date must be in the future')]
    #[Groups(['booking:read:item', 'booking:write'])]
    private ?\DateTimeInterface $bookingDate = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'Number of people must not be null')]
    #[Assert\Positive(message: 'Number of people must be a positive number')]
    #[Assert\LessThanOrEqual(value: 100, message: 'Number of people cannot exceed 100')]
    #[Groups(['booking:read:item', 'booking:write'])]
    private ?int $numberOfPeople = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Total price is required')]
    #[Assert\PositiveOrZero(message: 'Total price must be a positive number or zero')]
    #[Groups(['booking:read:item', 'booking:write'])]
    private ?string $totalPrice = null;

    /**
     * @var Collection<int, Payment>
     */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'booking')]
    #[Assert\Valid]
    #[Groups(['booking:read:item', 'booking:write'])]
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
        return $this->bookingDate;
    }

    public function setBookingDate(\DateTimeInterface $bookingDate): static
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }

    public function getNumberOfPeople(): ?int
    {
        return $this->numberOfPeople;
    }

    public function setNumberOfPeople(int $numberOfPeople): static
    {
        $this->numberOfPeople = $numberOfPeople;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

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
            if ($amount->getBooking() === $this) {
                $amount->setBooking(null);
            }
        }

        return $this;
    }
}

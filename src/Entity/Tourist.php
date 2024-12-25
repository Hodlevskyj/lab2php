<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\TouristRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['tourist:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['tourist:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['tourist:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['tourist:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: TouristRepository::class)]
class Tourist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tourist:read:collection', 'tourist:read:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'First name cannot exceed {{ limit }} characters'
    )]
    #[Groups(['tourist:read:collection', 'tourist:read:item', 'tourist:write'])]
    private ?string $first_name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Last name cannot exceed {{ limit }} characters'
    )]
    #[Groups(['tourist:read:collection', 'tourist:read:item', 'tourist:write'])]
    private ?string $last_name = null;

    #[ORM\Column(length: 100, unique: true)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Please enter a valid email address')]
    #[Groups(['tourist:read:item', 'tourist:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Regex(
        pattern: '/^\+?[0-9]{7,15}$/',
        message: 'Please enter a valid phone number'
    )]
    #[Groups(['tourist:read:item', 'tourist:write'])]
    private ?string $phone = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Registration date is required')]
    #[Assert\Type(
        type: \DateTimeInterface::class,
        message: 'Invalid date format for registration date'
    )]
    #[Assert\LessThanOrEqual('now', message: 'Registration date cannot be in the future')]
    #[Groups(['tourist:read:item', 'tourist:write'])]
    private ?\DateTimeInterface $registration_date = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'tourist')]
    #[Groups(['tourist:read:item'])]
    private Collection $bookings;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'tourist')]
    #[Groups(['tourist:read:item'])]
    private Collection $rating;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->rating = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registration_date;
    }

    public function setRegistrationDate(\DateTimeInterface $registration_date): static
    {
        $this->registration_date = $registration_date;

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
            $booking->setTourist($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getTourist() === $this) {
                $booking->setTourist(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getRating(): Collection
    {
        return $this->rating;
    }

    public function addRating(Review $rating): static
    {
        if (!$this->rating->contains($rating)) {
            $this->rating->add($rating);
            $rating->setTourist($this);
        }

        return $this;
    }

    public function removeRating(Review $rating): static
    {
        if ($this->rating->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getTourist() === $this) {
                $rating->setTourist(null);
            }
        }

        return $this;
    }
}

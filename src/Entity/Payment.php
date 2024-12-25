<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['payment:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['payment:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['payment:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['payment:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['payment:read:collection', 'payment:read:item'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Booking must not be null')]
    #[Groups(['payment:read:item', 'payment:write'])]
    private ?Booking $booking = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: 'Payment amount is required')]
    #[Assert\Positive(message: 'Payment amount must be greater than zero')]
    #[Groups(['payment:read:collection', 'payment:read:item', 'payment:write'])]
    private ?string $amount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Payment date is required')]
    #[Assert\Type(\DateTimeInterface::class, message: 'Invalid date format')]
    #[Assert\LessThanOrEqual('now', message: 'Payment date cannot be in the future')]
    #[Groups(['payment:read:item', 'payment:write'])]
    private ?\DateTimeInterface $payment_date = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['Pending', 'Completed', 'Failed', 'Refunded'],
        message: 'Status must be one of the following: Pending, Completed, Failed, Refunded'
    )]
    #[Groups(['payment:read:item', 'payment:write'])]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): static
    {
        $this->booking = $booking;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->payment_date;
    }

    public function setPaymentDate(\DateTimeInterface $payment_date): static
    {
        $this->payment_date = $payment_date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }
}

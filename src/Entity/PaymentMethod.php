<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\PaymentMethodRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['payment_method:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['payment_method:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['payment_method:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['payment_method:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: PaymentMethodRepository::class)]
class PaymentMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['payment_method:read:collection', 'payment_method:read:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Payment method is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Payment method cannot be longer than {{ limit }} characters'
    )]
    #[Groups(['payment_method:read:collection', 'payment_method:read:item', 'payment_method:write'])]
    private ?string $method = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }
}

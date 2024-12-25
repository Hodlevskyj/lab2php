<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\DestinationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['destination:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['destination:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['destination:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['destination:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: DestinationRepository::class)]
class Destination
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'The name cannot be longer than {{ limit }} characters'
    )]
    #[Groups(['destination:read:collection', 'destination:read:item', 'destination:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Description is required')]
    #[Assert\Length(
        max: 5000,
        maxMessage: 'The description cannot exceed {{ limit }} characters'
    )]
    #[Groups(['destination:read:item', 'destination:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Country is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'The country name cannot be longer than {{ limit }} characters'
    )]
    #[Groups(['destination:read:collection', 'destination:read:item', 'destination:write'])]
    private ?string $country = null;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }
}

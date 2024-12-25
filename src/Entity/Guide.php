<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\GuideRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['guide:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['guide:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['guide:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['guide:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: GuideRepository::class)]
class Guide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['guide:read:collection', 'guide:read:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'First name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'First name cannot be longer than {{ limit }} characters'
    )]
    #[Groups(['guide:read:collection', 'guide:read:item', 'guide:write'])]
    private ?string $first_name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Last name is required')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Last name cannot be longer than {{ limit }} characters'
    )]
    #[Groups(['guide:read:collection', 'guide:read:item', 'guide:write'])]
    private ?string $last_name = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'The email "{{ value }}" is not a valid email.')]
    #[Groups(['guide:read:item', 'guide:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Length(
        max: 15,
        maxMessage: 'Phone number cannot be longer than {{ limit }} characters'
    )]
    #[Assert\Regex(
        pattern: "/^\+?[0-9]*$/",
        message: 'Phone number can only contain numbers and an optional leading "+"'
    )]
    #[Groups(['guide:read:item', 'guide:write'])]
    private ?string $phone = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Language is required')]
    #[Assert\Length(
        max: 50,
        maxMessage: 'Language cannot be longer than {{ limit }} characters'
    )]
    #[Groups(['guide:read:item', 'guide:write'])]
    private ?string $language = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Bio is required')]
    #[Assert\Length(
        max: 2000,
        maxMessage: 'Bio cannot be longer than {{ limit }} characters'
    )]
    #[Groups(['guide:read:item', 'guide:write'])]
    private ?string $bio = null;

    /**
     * @var Collection<int, TourGuide>
     */
    #[ORM\OneToMany(targetEntity: TourGuide::class, mappedBy: 'guide')]
    #[Assert\Valid]
    #[Groups(['guide:read:item', 'guide:write'])]
    private Collection $tourGuides;

    public function __construct()
    {
        $this->tourGuides = new ArrayCollection();
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

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(string $bio): static
    {
        $this->bio = $bio;

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
            $tourGuide->setGuide($this);
        }

        return $this;
    }

    public function removeTourGuide(TourGuide $tourGuide): static
    {
        if ($this->tourGuides->removeElement($tourGuide)) {
            // set the owning side to null (unless already changed)
            if ($tourGuide->getGuide() === $this) {
                $tourGuide->setGuide(null);
            }
        }

        return $this;
    }
}

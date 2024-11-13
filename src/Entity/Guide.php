<?php

namespace App\Entity;

use App\Repository\GuideRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GuideRepository::class)]
class Guide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $first_name = null;

    #[ORM\Column(length: 100)]
    private ?string $last_name = null;

    #[ORM\Column(length: 100)]
    private ?string $email = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 50)]
    private ?string $language = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $bio = null;

    /**
     * @var Collection<int, TourGuide>
     */
    #[ORM\OneToMany(targetEntity: TourGuide::class, mappedBy: 'guide')]
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

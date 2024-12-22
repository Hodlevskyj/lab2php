<?php

namespace App\Entity;

use App\Repository\TourGuideRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TourGuideRepository::class)]
class TourGuide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tourGuides')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'A tour must be associated with a tour guide')]
    private ?Tour $tour = null;

    #[ORM\ManyToOne(inversedBy: 'tourGuides')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'A guide must be assigned to the tour')]
    private ?Guide $guide = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGuide(): ?Guide
    {
        return $this->guide;
    }

    public function setGuide(?Guide $guide): static
    {
        $this->guide = $guide;

        return $this;
    }
}

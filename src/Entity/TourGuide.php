<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\TourGuideRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['tour_guide:read:collection']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['tour_guide:write']]
        ),
        new Get(
            normalizationContext: ['groups' => ['tour_guide:read:item']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['tour_guide:write']]
        ),
        new Delete()
    ]
)]
#[ORM\Entity(repositoryClass: TourGuideRepository::class)]
class TourGuide
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tour_guide:read:collection', 'tour_guide:read:item'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'tourGuides')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'A tour must be associated with a tour guide')]
    #[Groups(['tour_guide:read:collection', 'tour_guide:read:item', 'tour_guide:write'])]
    private ?Tour $tour = null;

    #[ORM\ManyToOne(inversedBy: 'tourGuides')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'A guide must be assigned to the tour')]
    #[Groups(['tour_guide:read:collection', 'tour_guide:read:item', 'tour_guide:write'])]
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

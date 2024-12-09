<?php

namespace App\Service;

use App\Entity\Tour;
use App\Entity\Guide;
use App\Entity\TourGuide;
use Doctrine\ORM\EntityManagerInterface;

class TourGuideService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createTourGuide(Tour $tour, Guide $guide): TourGuide
    {
        $tourGuide = new TourGuide();

        $tourGuide->setTour($tour)
            ->setGuide($guide);

        $this->entityManager->persist($tourGuide);
        $this->entityManager->flush();

        return $tourGuide;
    }
}

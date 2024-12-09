<?php

namespace App\Service;

use App\Entity\Tour;
use Doctrine\ORM\EntityManagerInterface;

class TourService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createTour(string $name, string $description, int $duration,string $price): Tour
    {
        $tour = new Tour();

        $tour->setName($name)
            ->setDescription($description)
            ->setDuration($duration)
            ->setPrice($price);



        $this->entityManager->persist($tour);
        $this->entityManager->flush();

        return $tour;
    }
}
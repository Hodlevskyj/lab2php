<?php

namespace App\Service;

use App\Entity\Destination;
use Doctrine\ORM\EntityManagerInterface;

class DestinationService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createDestination(string $name, string $description, string $country): Destination
    {
        $destination = new Destination();

        $destination->setName($name)
        ->setDescription($description)
        ->setCountry($country);



        $this->entityManager->persist($destination);
        $this->entityManager->flush();

        return $destination;
    }
}
<?php

namespace App\Service;

use App\Entity\Tourist;
use Doctrine\ORM\EntityManagerInterface;

class TouristService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createTourist(string $firstname, string $lastname, string $email, string $phone): Tourist
    {
        $tourist = new Tourist();

        $tourist->setFirstName($firstname)
            ->setLastName($lastname)
            ->setEmail($email)
            ->setPhone($phone)
            ->setRegistrationDate(new \DateTime());


        $this->entityManager->persist($tourist);
        $this->entityManager->flush();

        return $tourist;
    }
}
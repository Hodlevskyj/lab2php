<?php

namespace App\Service;

use App\Entity\Guide;
use Doctrine\ORM\EntityManagerInterface;

class GuideService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createGuide(string $firstname, string $lastname, string $email, string $phone, string $language, string $bio): Guide
    {
        $guide = new Guide();

        $guide->setFirstName($firstname)
        ->setLastName($lastname)
        ->setEmail($email)
        ->setPhone($phone)
        ->setLanguage($language)
        ->setBio($bio);



        $this->entityManager->persist($guide);
        $this->entityManager->flush();

        return $guide;
    }
}
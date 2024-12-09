<?php

namespace App\Service;

use App\Entity\Review;
use App\Entity\Tour;
use App\Entity\Tourist;
use Doctrine\ORM\EntityManagerInterface;

class ReviewService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createReview(Tourist $tourist, Tour $tour, int $rating, string $comment): Review
    {
        $review = new Review();

        $review->setTourist($tourist)
            ->setTour($tour)
            ->setRating($rating)
            ->setComment($comment)
            ->setReviewDate(new \DateTime());

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return $review;
    }
}

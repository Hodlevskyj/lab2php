<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Tour;
use App\Entity\Tourist;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createBooking(
        Tourist $tourist,
        Tour $tour,
        \DateTimeInterface $bookingDate,
        int $numberOfPeople,
        string $totalPrice
    ): Booking {
        $booking = new Booking();
        $booking->setTourist($tourist);
        $booking->setTour($tour);
        $booking->setBookingDate($bookingDate);
        $booking->setNumberOfPeople($numberOfPeople);
        $booking->setTotalPrice($totalPrice);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        return $booking;
    }
}

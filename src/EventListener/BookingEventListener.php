<?php

namespace App\EventListener;

use App\Entity\Booking;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class BookingEventListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function postUpdate(Booking $booking, LifecycleEventArgs $event): void
    {
        $this->logger->info('A booking has been successfully updated.', [
            'bookingId' => $booking->getId(),
            'tourist' => $booking->getTourist()->getName(),
            'tour' => $booking->getTour()->getName(),
            'numberOfPeople' => $booking->getNumberOfPeople(),
            'totalPrice' => $booking->getTotalPrice(),
        ]);
    }

}

<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;


class PaymentService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createPayment(Booking $booking, string $amount, \DateTimeInterface $paymentDate, string $status): Payment
    {
        $payment = new Payment();

        $payment->setBooking($booking)
            ->setAmount($amount)
            ->setPaymentDate($paymentDate)
            ->setStatus($status);

        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }
}

<?php

namespace App\Service;

use App\Entity\PaymentMethod;
use Doctrine\ORM\EntityManagerInterface;

class PaymentMethodService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createPaymentMethod(string $method): PaymentMethod
    {
        $paymentMethod = new PaymentMethod();

        $paymentMethod->setMethod($method);



        $this->entityManager->persist($paymentMethod);
        $this->entityManager->flush();

        return $paymentMethod;
    }
}
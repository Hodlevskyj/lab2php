<?php

namespace App\Action;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CompleteBookingAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(int $id): JsonResponse
    {
        $booking = $this->entityManager->getRepository(Booking::class)->find($id);

        if (!$booking) {
            throw new NotFoundHttpException('Booking not found.');
        }

        if ($booking->getStatus() === 'completed') {
            throw new BadRequestHttpException('This booking has already been completed.');
        }

        $booking->setStatus('completed');
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'booking' => $booking], 200);
    }
}

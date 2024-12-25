<?php

namespace App\Action;

use App\Entity\Destination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CompleteDestinationAction
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(int $id, string $newName): JsonResponse
    {
        $destination = $this->entityManager->getRepository(Destination::class)->find($id);

        if (!$destination) {
            throw new NotFoundHttpException('Destination not found.');
        }

        if (empty($newName)) {
            throw new BadRequestHttpException('The new name cannot be empty.');
        }

        $destination->setName($newName);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'destination' => $destination], 200);
    }
}

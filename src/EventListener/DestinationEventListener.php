<?php

namespace App\EventListener;

use App\Entity\Destination;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;

class DestinationEventListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function postUpdate(Destination $destination, LifecycleEventArgs $event): void
    {
        $this->logger->info('A Destination has been updated.', [
            'id' => $destination->getId(),
            'name' => $destination->getName(),
        ]);
    }
}

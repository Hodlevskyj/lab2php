<?php

namespace App\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

abstract class BookingExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ($resourceClass !== 'App\\Entity\\Booking') {
            return;
        }

        $this->addFilters($queryBuilder);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ($resourceClass !== 'App\\Entity\\Booking') {
            return;
        }

        $this->addFilters($queryBuilder);
    }

    private function addFilters(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if ($this->security->getUser()) {
            $queryBuilder->andWhere(sprintf('%s.tourist = :currentUser', $rootAlias))
                ->setParameter('currentUser', $this->security->getUser());
        }

        $queryBuilder->andWhere(sprintf('%s.bookingDate >= :today', $rootAlias))
            ->setParameter('today', new \DateTimeImmutable('today'));
    }
}

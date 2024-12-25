<?php

declare(strict_types=1);

namespace App\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

abstract class DestinationExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    private const FIRST_ELEMENT_INDEX = 0;

    public function __construct(Security $security)
    {
        parent::__construct($security);
    }

    public function getResourceClass(): string
    {
        return 'App\\Entity\\Destination';
    }

    public function buildQuery(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[self::FIRST_ELEMENT_INDEX];

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $queryBuilder->andWhere(sprintf('%s.country = :userCountry', $rootAlias))
                ->setParameter('userCountry', 'France');
        }
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []
    ): void {
        if ($this->isFiltering($operation, $resourceClass)) {
            return;
        }
        $this->buildQuery($queryBuilder);
    }
}

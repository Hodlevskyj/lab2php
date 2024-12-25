<?php

namespace App\Repository;

use App\Entity\TourGuide;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<TourGuide>
 */
class TourGuideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TourGuide::class);
    }

    public function getPaginatedTourGuides(int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->orderBy('t.id', 'ASC'); // Сортування за ID

        // Використовуємо Paginator
        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator); // Загальна кількість записів
        $totalPages = ceil($totalItems / $itemsPerPage); // Загальна кількість сторінок

        // Налаштування пагінації
        $queryBuilder
            ->setFirstResult($itemsPerPage * ($page - 1)) // Перший запис на сторінці
            ->setMaxResults($itemsPerPage); // Максимальна кількість записів на сторінку

        return [
            'tourGuides' => $paginator->getQuery()->getResult(), // Список турів-гайдів для сторінки
            'totalItems' => $totalItems, // Загальна кількість записів
            'totalPages' => $totalPages, // Загальна кількість сторінок
        ];
    }

    //    /**
    //     * @return TourGuide[] Returns an array of TourGuide objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TourGuide
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
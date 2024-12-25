<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function getPaginatedReviews(int $itemsPerPage, int $page): array
    {
        $queryBuilder = $this->createQueryBuilder('r') // 'r' — псевдонім для таблиці `Review`
        ->orderBy('r.id', 'ASC'); // Сортуємо за ID

        // Використовуємо Paginator
        $paginator = new Paginator($queryBuilder);

        $totalItems = count($paginator); // Загальна кількість записів
        $totalPages = ceil($totalItems / $itemsPerPage); // Загальна кількість сторінок

        // Налаштування пагінації
        $queryBuilder
            ->setFirstResult($itemsPerPage * ($page - 1)) // Перший запис на сторінці
            ->setMaxResults($itemsPerPage); // Максимальна кількість записів на сторінку

        return [
            'reviews' => $paginator->getQuery()->getResult(), // Список відгуків для поточної сторінки
            'totalItems' => $totalItems, // Загальна кількість записів
            'totalPages' => $totalPages, // Загальна кількість сторінок
        ];
    }

    //    /**
    //     * @return Review[] Returns an array of Review objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Review
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
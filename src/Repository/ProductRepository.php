<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
     public function __construct(ManagerRegistry $registry)
     {
         parent::__construct($registry, Product::class);
     }

    /**
     * @return Product[]
     */
    public function findAllPublished(): array
    {
        return $this->findBy(['isPublished' => true]);
    }

    /**
     * @param string $term
     * @return Product[]
     */
    public function search(string $term): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->getQuery()
            ->execute();
    }
}
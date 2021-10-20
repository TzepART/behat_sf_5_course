<?php

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
    public function findAllPublished()
    {
        return $this->findBy(array(
            'isPublished' => true
        ));
    }

    /**
     * @param string $term
     * @return Product[]
     */
    public function search($term)
    {
        if (!$term) {
            return $this->findAll();
        }

        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :term')
            ->setParameter('term', '%'.$term.'%')
            ->getQuery()
            ->execute();
    }
}
<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProductParameter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductParameter>
 */
class ProductParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductParameter::class);
    }

    public function findParameterValuesForProduct(int $productId): array
    {
        return $this->createQueryBuilder('pp')
            ->select('param.code, param.name, pv.value, pv.name as value_name')
            ->innerJoin('pp.parameter', 'param')
            ->innerJoin('param.values', 'pv')
            ->where('pp.product = :productId')
            ->setParameter('productId', $productId)
            ->orderBy('param.name', 'ASC')
            ->addOrderBy('pv.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

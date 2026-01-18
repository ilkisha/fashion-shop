<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findActive(?string $gender = null, ?string $category = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.id', 'DESC');

        if ($gender) {
            $qb->andWhere('p.gender = :gender')
                ->setParameter('gender', $gender);
        }

        if ($category) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneActiveBySlug(string $slug): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.isActive = :active')
            ->andWhere('p.slug = :slug')
            ->setParameter('active', true)
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findActiveCategories(?string $gender = null): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('DISTINCT p.category')
            ->andWhere('p.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('p.category', 'ASC');

        if ($gender) {
            $qb->andWhere('p.gender = :gender')
                ->setParameter('gender', $gender);
        }

        $rows = $qb->getQuery()->getArrayResult();

        return array_values(array_map(static fn (array $r) => $r['category'], $rows));
    }
}

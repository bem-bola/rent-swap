<?php

namespace App\Repository;

use App\Entity\Category;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorService $paginatorService
    )
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * categories by name
     * @return Category[] Returns an array of Category objects
     */
    public function findByLikeByName(string $name): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.name')
            ->andWhere('LOWER(c.name) LIKE :name')
            ->setParameter('name', '%' . strtolower($name) .'%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function length(): ?int{
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAll(array $queryParams):array {
        return $this->paginatorService->dataTableByQueryBuilder(
            $this->createQueryBuilder('c'),
            $queryParams,
        );
    }
}

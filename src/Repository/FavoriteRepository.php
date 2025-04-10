<?php

namespace App\Repository;

use App\Entity\Favorite;
use App\Entity\User;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,
                                private PaginatorService $paginatorService)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function findByUser(array $params, User $user): array
    {
        $filters = $params['filters'] ?? [];

        $sort = $params['orderby'] ?? [];

        $query = $this->createQueryBuilder('f')
            ->join('f.device', 'd')
            ->join('f.user', 'u')
            ->andWhere('u.id = :user')
            ->andWhere('f.isFavorite = :isFavorite')
            ->setParameter('user', $user)
            ->setParameter('isFavorite', true)
        ;

        if(isset($filters['title']) && $filters['title'] != null) {
            $query->andWhere('LOWER(d.title) LIKE :title')
                ->setParameter('title', '%'.strtolower($filters['title']).'%');
        }
        if(isset($sort['price']) && $sort['price'] != null) {
            $query->orderBy('d.price', $sort['price']);
        }

        return $this->paginatorService->getDatas($query, $params['pagination'] ?? []);
    }

}
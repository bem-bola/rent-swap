<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Entity\User;
use App\Mapping\DeviceMapping;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviceRepository extends ServiceEntityRepository
{


    public function __construct(
        ManagerRegistry          $registry,
        private PaginatorService $paginatorService,
        private DeviceMapping     $deviceMapping
    )
    {
        parent::__construct($registry, Device::class);
    }
    public function save(Device $device): void{
        $this->getEntityManager()->persist($device);
        $this->getEntityManager()->flush();
    }

    public function getDeviceBySlug(string $slug)
    {
        $query = $this->createQueryBuilder('d');

        $query->andWhere('d.slug = :slug')
            ->setParameter('slug', $slug)
            ->join(DevicePicture::class, 'dp');

        return $query->getQuery()->getResult();
    }

    public function findSimilarDevices(Device $device)
    {
        return $this->createQueryBuilder('d')
            ->join('d.categories', 'c')
            ->where('c IN (:deviceCats)')
            ->andWhere('d.id != :deviceId')
            ->setParameter('deviceCats', $device->getCategories()->toArray())
            ->setParameter('deviceId', $device->getId())
            ->groupBy('d.id')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();
    }

    public function findBySlugCategory(string $slug, int $page = 1, int $limit = 10): array
    {
        $query =  $this->createQueryBuilder('d')
            ->join('d.categories', 'c')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug);

        return $this->paginatorService->getDatas($query);

    }


    public function findByFilters(array $params): array
    {

        $filters = $params['filters'] ?? [];

        $sort = $params['orderby'] ?? [];

        $query = $this->createQueryBuilder('d');
        if(isset($filters['title']) && $filters['title'] != null) {
            $query->andWhere('LOWER(d.title) LIKE :title')
                ->setParameter('title', '%'.strtolower($filters['title']).'%');
        }
        if(isset($filters['category']) && $filters['category'] != null) {
            $query->join('d.categories', 'c')
                ->andWhere('c.slug = :slug')
            ->setParameter('slug', $filters['category']);
        }
        if(isset($filters['location']) && $filters['location'] != null) {
            $query->andWhere('d.location LIKE :location')
                ->setParameter('location', '%'.$filters['location'].'%');
        }
        if(isset($filters['priceMin']) && $filters['priceMin'] != null) {
            $query->andWhere('d.price > :priceMin')
                ->setParameter('priceMin', (float)$filters['priceMin']);;
        }
        if(isset($filters['priceMax']) && $filters['priceMax'] != null) {
            $query->andWhere('d.price < :priceMax')
                ->setParameter('priceMax', (float)$filters['priceMax']);
        }
        if(isset($sort['price']) && $sort['price'] != null) {
            $query->orderBy('d.price', $sort['price']);
        }

        return $this->paginatorService->getDatas($query, $params['pagination'] ?? []);
    }
    public function findLatestVersionByParent(Device $device): ?Device
    {

        if(!$device->getParent()) return $device;
        return $this->createQueryBuilder('d')
            ->where('d.parent = :parent')
            ->setParameter('parent', $device->getParent())
            ->orderBy('d.created', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUser(array $params, User $user)
    {
        $filters = $params['filters'] ?? [];

        $sort = $params['orderby'] ?? [];

        $sql = "SELECT d.*
                FROM device d
                LEFT JOIN (
                    SELECT parent_id, MAX(created) AS max_created
                    FROM device
                    WHERE parent_id IS NOT NULL
                    GROUP BY parent_id
                ) latest ON d.parent_id = latest.parent_id AND d.created = latest.max_created
                WHERE (d.parent_id IS NULL OR d.created = latest.max_created) 
                    AND d.user_id = :userId
                ";

        $parameters = ['userId' => $user->getId()];

        if(isset($filters['status']) && $filters['status'] != null) {
            $sql .= " AND d.status = :status";
            $parameters['status'] = $filters['status'];
        }
        if(isset($filters['deleted']) && $filters['deleted'] != null) {
            $sql .= " AND d.deleted IS NOT NULL";
        }

        if(isset($filters['title']) && $filters['title'] != null) {
            $sql .= " AND LOWER(d.title) LIKE :title";
            $parameters['title'] = '%'.strtolower($filters['title']).'%';
        }

        return $this->paginatorService->getDatasPaginator(
            $this->deviceMapping->createMapping(),
            $sql,
            $parameters,
            $params['pagination'] ?? []
        );
    }
}
<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Entity\User;
use App\Mapping\DeviceMapping;
use App\Service\Constances;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
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


    /**
     * @throws Exception
     */
    public function findByFilters(array $params): array
    {
        $params['filters']['status'] = Constances::VALIDED;

        $sql = "SELECT DISTINCT d.*
                FROM device d
                LEFT JOIN (
                    SELECT parent_id, MAX(created) AS max_created
                    FROM device
                    WHERE parent_id IS NOT NULL
                    GROUP BY parent_id
                ) latest ON d.parent_id = latest.parent_id AND d.created = latest.max_created
                JOIN category_device cd ON cd.device_id = d.id 
                     JOIN category c ON cd.category_id = c.id
                    JOIN user ON user.id = d.user_id
                AND (d.parent_id IS NULL OR d.created = latest.max_created) 
                    AND (d.deleted IS NULL)
                AND (user.deleted != null OR user.is_deleted = true OR user.banned != null OR user.is_banned = true OR user.suspended != null OR user.is_suspended = true)
                ";

        return $this->paginatorService->getDatasPaginator(
            $this->deviceMapping->createMapping(),
            $sql,
            $params
        );

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

    /**
     * @throws Exception
     */
    public function findByUser(array $params, User $user, bool $count = false): array|int|null
    {
        $params['filters']['user_id'] = $user->getId();

        $sql = "SELECT d.*
                FROM device d
                LEFT JOIN (
                    SELECT parent_id, MAX(created) AS max_created
                    FROM device
                    WHERE parent_id IS NOT NULL
                    GROUP BY parent_id
                ) latest ON d.parent_id = latest.parent_id AND d.created = latest.max_created
                WHERE (d.parent_id IS NULL OR d.created = latest.max_created) 
                    AND (d.deleted IS NULL)
                    AND d.status = 'validated'
        
                ";

        return $this->paginatorService->getDatasPaginator(
            $this->deviceMapping->createMapping(),
            $sql,
            $params,
            $count
        );
    }


    public function findLastByUser(User $user){
        return $this->createQueryBuilder('d')
            ->where('d.user = :user')
            ->andWhere('d.deleted IS NULL')
            ->setParameter('user', $user)
            ->orderBy('d.created', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }


    /**
     * @param array $params
     * @param bool $count
     * @return array|int|null
     * @throws Exception
     */
    public function findAllNotDelete(array $params, bool $count = false)
    {

        $sql = "SELECT d.*
                FROM device d
                LEFT JOIN (
                    SELECT parent_id, MAX(created) AS max_created
                    FROM device
                    WHERE parent_id IS NOT NULL
                    GROUP BY parent_id
                ) latest ON d.parent_id = latest.parent_id AND d.created = latest.max_created
                WHERE (d.parent_id IS NULL OR d.created = latest.max_created) 
                    AND d.deleted IS NULL
                ";


        return $this->paginatorService->getDatasPaginator(
            $this->deviceMapping->createMapping(),
            $sql,
            $params,
            $count
        );
    }


    /**
     * @param array $queryParams
     * @param bool $count
     * @return array
     * @throws Exception
     */
    public function deviceDataTableNoDelete(array $queryParams, bool $count = false):array
    {

        $sql = "SELECT d.*
                FROM device d
                LEFT JOIN (
                    SELECT parent_id, MAX(created) AS max_created
                    FROM device
                    WHERE parent_id IS NOT NULL
                    GROUP BY parent_id
                ) latest ON d.parent_id = latest.parent_id AND d.created = latest.max_created
                WHERE (d.parent_id IS NULL OR d.created = latest.max_created) 
                    AND d.deleted IS NULL
                ";

        return $this->paginatorService->dataDataBleDeviceBySql(
            $this->deviceMapping->createMapping(),
            $sql,
            $queryParams,
            $count
        );
    }

    /**
     * @throws Exception
     */
    public function historyDevice(array $queryParams, Device $device, bool $count = false):array
    {

        $queryParams['search']['parent_id'] = $device->getParent() ? $device->getParent()->getId() : $device->getId();

        $sql = "SELECT d.* FROM device d WHERE d.created is not null";

        return $this->paginatorService->dataDataBleDeviceBySql(
            $this->deviceMapping->createMapping(),
            $sql,
            $queryParams
        );
    }

    /**
     * @throws Exception
     */
    public function getByUserDataTable(array $queryParams, int $idUser):array
    {

        $queryParams['search']['user_id'] = $idUser;

        $sql = "select * from (SELECT d.*
                FROM device d
                LEFT JOIN (
                    SELECT parent_id, MAX(created) AS max_created
                    FROM device
                    WHERE parent_id IS NOT NULL
                    GROUP BY parent_id
                ) latest ON d.parent_id = latest.parent_id AND d.created = latest.max_created
                WHERE (d.parent_id IS NULL OR d.created = latest.max_created)) `dld.*` 
                ";

        return $this->paginatorService->dataDataBleDeviceBySql(
            $this->deviceMapping->createMapping(),
            $sql,
            $queryParams
        );
    }
}
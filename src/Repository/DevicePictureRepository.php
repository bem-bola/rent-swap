<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DevicePictureRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorService $paginatorService)
    {
        parent::__construct($registry, DevicePicture::class);
    }

    /**
     * @param Device $device
     * @param array $params
     * @return array
     */
    public function findByParent(Device $device, array $params = []): array
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.device = :device')
            ->andWhere('p.deleted is null');

        if($device->getParent() !== null)
            $query->setParameter('device', $device->getParent());
        else
            $query->setParameter('device', $device);

        return $this->paginatorService->getDatas($query, $params['pagination'] ?? []);
    }

    public function findByParentFirst(Device $device)
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.device = :device')
            ->andWhere('p.deleted is null');

        if($device->getParent() !== null)
            $query->setParameter('device', $device->getParent());
        else
            $query->setParameter('device', $device);

        return $query->setMaxResults(1)->getQuery()->getOneOrNullResult();
    }

}
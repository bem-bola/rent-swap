<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\DevicePicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }
    public function getDeviceBySlug(string $slug){
        $query = $this->createQueryBuilder('d');

        $query->andWhere('d.slug = :slug')
            ->setParameter('slug', $slug)
            ->join(DevicePicture::class, 'dp');

        return $query->getQuery()->getResult();

    }

}
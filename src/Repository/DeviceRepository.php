<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
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
            ->join('d.subCategories', 'c')
            ->where('c IN (:deviceCats)')
            ->andWhere('d.id != :deviceId')
            ->setParameter('deviceCats', $device->getSubCategories()->toArray())
            ->setParameter('deviceId', $device->getId())
            ->groupBy('d.id')
            ->setMaxResults(12)
            ->getQuery()
            ->getResult();
    }
}
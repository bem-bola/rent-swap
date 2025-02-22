<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Entity\TypeDevice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TypeDeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeDevice::class);
    }


}
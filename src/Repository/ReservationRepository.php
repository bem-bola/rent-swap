<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\Reservation;
use App\Service\Constances;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * @param Device $device
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getOneReservationByDates(Device $device, \DateTime $startDate, \DateTime $endDate){

        return $this->createQueryBuilder('r')
            ->select('r.id')
            ->andWhere('r.device = :device')
            ->andWhere('r.started >= :started' )
            ->andWhere('r.ended <= :ended' )
            ->andWhere('r.status != :status' )
            ->setParameter('device', $device)
            ->setParameter('started', $startDate)
            ->setParameter('ended', $endDate)
            ->setParameter('status', Constances::ACCEPTED)
            ->getQuery()
            ->getOneOrNullResult();

    }

}
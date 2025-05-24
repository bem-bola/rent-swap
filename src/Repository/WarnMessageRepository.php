<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use App\Entity\WarnMessage;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WarnMessage>
 */
class WarnMessageRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorService $paginatorService
    )
    {
        parent::__construct($registry, WarnMessage::class);
    }

    /**
     * @return WarnMessage[] Returns an array of WarnMessage objects
     */
    public function findByInformantMessage(User $informant, Message $message): array
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.message = :message')
            ->andWhere('w.informant = :informant')
            ->setParameter('message', $message)
            ->setParameter('informant', $informant)
            ->getQuery()
            ->getResult()
        ;
    }

    public function getAll(array $queryParams, bool $reviewer = false):array {
        $queryBuilder = $this->createQueryBuilder('w')
            ->join('w.informant', 'informant')
            ->join('w.message', 'm')
            ->join('m.author', 'author')
            ->leftjoin('w.reviewer', 'reviewer')
        ;

        if($reviewer === true) {
            $queryBuilder->andWhere('w.reviewed is not null');
        }else{
            $queryBuilder->andWhere('w.reviewed is null');
        }

        return $this->paginatorService->dataTableByQueryBuilder(
            $queryBuilder,
            $queryParams,
        );
    }


}

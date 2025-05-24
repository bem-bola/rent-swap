<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Device;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * @param Conversation $conversation
     * @return void
     */
    public function save(Conversation $conversation): void{
        $this->getEntityManager()->persist($conversation);
        $this->getEntityManager()->flush();
    }

    /**
     * @param User $sender
     * @param User $recipient
     * @return Conversation|null
     */
    public function findOneByUsers(User $sender, User $recipient): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->where(':sender MEMBER OF c.users')
            ->andWhere(':recipient MEMBER OF c.users')
            ->setParameter('sender', $sender)
            ->setParameter('recipient', $recipient)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param User $user
     * @return array|null
     */
    public function findAllByUser(User $user): ?array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.users', 'u')
            ->leftJoin('c.messages', 'm')
            ->where('u = :user')
            ->setParameter('user', $user)
            ->groupBy('c.id')
            ->orderBy('MAX(m.createdAt)', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByUser(User $user): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c) AS length')
            ->where(':user MEMBER OF c.users')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
           ;
    }

    public function countByDevice(Device $device, User $user): ?array
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c) AS length')
            ->where(':user MEMBER OF c.users')
            ->andWhere('c.device = :device')
            ->setParameter('user', $user)
            ->setParameter('device', $device)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
}

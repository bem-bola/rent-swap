<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param Message $message
     * @return void
     */
    public function save(Message $message): void{
        $this->getEntityManager()->persist($message);
        $this->getEntityManager()->flush();
    }


    public function findOneBySlugConversation(string $slugConversation): array
    {
        $messages = $this->createQueryBuilder('m')
            ->join('m.conversation', 'c')
            ->where('c.slug = :slug')
            ->setParameter('slug', $slugConversation)
            ->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        return array_reduce($messages, function ($carry, $message) {
            $dateKey = $message->getCreatedAt()->format('Y-m-d');
            $carry[$dateKey][] = $message;
            return $carry;
        }, []);
    }
}

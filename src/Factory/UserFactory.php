<?php

namespace App\Factory;

use App\Entity\Conversation;
use App\Entity\Media;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Service\Constances;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class UserFactory
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerService          $logger
    ) {}

    public function addAvatar(User $user, Media $media): User{
        $user->setAvatar($media);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->logger->write(
            Constances::LEVEL_INFO,
            "Avatar de user id: " . $user->getId() . " modifié",
            Response::HTTP_CREATED,
            $user
        );

        return $user;
    }
}
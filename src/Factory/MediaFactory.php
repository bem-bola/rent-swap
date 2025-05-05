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

class MediaFactory
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerService $logger,
    ) {}

    public function createByUser($file, string $filename, User $user): Media{
        $media = new Media();

        $media->setName($file->getClientOriginalName());
        $media->setCreated(new \DateTime());
        $media->setFilename($filename);
        $media->setAlt('Avatar ' . $user->getUsername());

        $this->entityManager->persist($media);
        $this->entityManager->flush();

        $this->logger->write(
            Constances::LEVEL_INFO,
            "Media id: " . $media->getId() . " créé",
            Response::HTTP_CREATED,
            $user
        );

        return $media;

    }
}
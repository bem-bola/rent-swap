<?php

namespace App\Factory;

use App\Entity\Message;
use App\Entity\User;
use App\Entity\WarnMessage;
use App\Service\Constances;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class WarnMessageFactory
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerService          $loggerService
    ){}
    public function create(User $informant, Message $message): WarnMessage{
        $warnMessage = new WarnMessage();

        $warnMessage->setInformant($informant);
        $warnMessage->setMessage($message);
        $warnMessage->setCreated(new \DateTime());

        $this->entityManager->persist($warnMessage);
        $this->entityManager->flush();

        $this->loggerService->write(Constances::LEVEL_INFO, sprintf("WarnMessage created %s", $warnMessage->getId()), Response::HTTP_CREATED, $informant);
        return $warnMessage;
    }

    public function updateReviewer(WarnMessage $warnMessage, User $reviewer): WarnMessage{
        $warnMessage->setReviewer($reviewer);
        $warnMessage->setReviewed(new \DateTime());

        $this->loggerService->write(Constances::LEVEL_INFO, sprintf("WarnMessage update %s", $warnMessage->getId()), Response::HTTP_CREATED, $reviewer);
        return $warnMessage;
    }
}
<?php

namespace App\Factory;

use App\Entity\Conversation;
use App\Entity\Device;
use App\Entity\Email;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\EmailRepository;
use App\Service\Constances;
use App\Service\LoggerService;

class EmailFactory
{
    public function __construct(
        private readonly LoggerService   $loggerService,
        private readonly EmailRepository $emailRepository) {}

    /**
     * @param User $sender
     * @param User $receiver
     * @param string $content
     * @param string $object
     * @param string|null $adressSender
     * @param Device|null $device
     * @return Email
     */
    public function create(User $sender,
                           User $receiver,
                           string $content,
                           string $object,
                           ?string $adressSender = null,
                           ?Device $device = null): Email
    {
        $email = new Email();

        $email->setContent($content);
        $email->setSender($sender);
        $email->setReceiver($receiver);
        $email->setAdressSender($adressSender);
        $email->setDevice($device);
        $email->setCreated(new \DateTime());
        $email->setObject($object);
        $this->emailRepository->save($email);

        $this->loggerService->write(Constances::LEVEL_INFO, sprintf("Mail envoyé par userId: %s à userId: %s", $sender->getId(), $receiver->getId()));


        return $email;
    }

}
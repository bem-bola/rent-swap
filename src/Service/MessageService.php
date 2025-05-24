<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Device;
use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageService
{

    public function __construct(
        private readonly LoggerService  $loggerService,

    )
    {}

    public function messageDefaultDevice(Device $device): Message{

        $message = new Message();
        $message->setContent(sprintf("Bonjour %s,\nVotre %s est disponible à la location.\nCordialement", $device->getUser()->getUsername(), $device->getTitle()));
        return $message;
    }

}

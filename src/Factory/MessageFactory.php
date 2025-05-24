<?php

namespace App\Factory;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Service\Constances;
use App\Service\LoggerService;
use Symfony\Component\HttpFoundation\Response;

class MessageFactory
{
    public function __construct(
        private readonly LoggerService      $loggerService,
        private readonly MessageRepository $messageRepository
    ) {}

    public function create(User $author, string $content, Conversation $conversation): Message
    {
        $message = new Message();

        $message->setContent($content);
        $message->setAuthor($author);
        $message->setConversation($conversation);
        $this->messageRepository->save($message);

        return $message;
    }

    public function delete(User $user, Message $message): Message
    {
        $message->setDeletedAt(new \DateTime());
        $this->messageRepository->save($message);
        $this->loggerService->write(Constances::LEVEL_INFO, sprintf('Message deleted %s', $message->getId()), Response::HTTP_OK, $user);
        return $message;
    }

}
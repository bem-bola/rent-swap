<?php

namespace App\Factory;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\MessageRepository;

class MessageFactory
{
    public function __construct(private readonly MessageRepository $messageRepository) {}

    public function create(User $author, string $content, Conversation $conversation): Message
    {
        $message = new Message();

        $message->setContent($content);
        $message->setAuthor($author);
        $message->setConversation($conversation);
        $this->messageRepository->save($message);

        return $message;
    }

}
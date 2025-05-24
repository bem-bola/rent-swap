<?php

namespace App\Factory;

use App\Entity\Conversation;
use App\Entity\Device;
use App\Entity\User;
use App\Repository\ConversationRepository;

class ConversationFactory
{
    public function __construct(private readonly ConversationRepository $conversationRepository) {}

    public function create(User $sender, User $recipient, ?Device $device = null): Conversation
    {
        $conversation = new Conversation();

        $conversation->addUser($sender);
        $conversation->addUser($recipient);
        $conversation->setDevice($device);
        $this->conversationRepository->save($conversation);

        return $conversation;
    }

}
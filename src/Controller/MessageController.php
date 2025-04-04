<?php

namespace App\Controller;

use App\DTO\CreateMessage;
use App\Entity\Message;
use App\Entity\User;
use App\Factory\ConversationFactory;
use App\Factory\MessageFactory;
use App\Repository\ConversationRepository;
use App\Repository\DeviceRepository;
use App\Repository\MessageRepository;
use App\Service\TopicService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

/**
 * @method User|null getUser()
 */
#[Route('/messages/', name: 'app_message_')]
class MessageController extends AbstractController
{

    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly HubInterface           $hub,
        private readonly LoggerInterface        $logger,
        private readonly MessageFactory         $messageFactory,
        private readonly TopicService           $topicService,
    )
    {
    }
    #[Route('', name: 'app_message_')]
    public function index()
    {
        return $this->render('message/index.html.twig', []);
    }

    #[Route('new-message', name: 'new', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createMessage(#[MapRequestPayload] CreateMessage $payload): Response{

        $sender = $this->getUser();

        $conversation = $this->conversationRepository->findOneBy(['slug' => $payload->slugConversation]);

        $message = $this->messageFactory->create($sender, $payload->content , $conversation);

        $data = [
            'author_id' => $message->getAuthor()->getId(),
            'content' => $message->getContent(),
        ];


        $update = new Update(
            topics: $this->topicService->getTopicUrl($conversation),
            data: json_encode($data),
            private: true
        );

        $this->hub->publish($update);

        return new Response('', Response::HTTP_CREATED);

    }
}

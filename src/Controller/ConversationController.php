<?php

namespace App\Controller;

use App\DTO\CreateMessage;
use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Factory\ConversationFactory;
use App\Factory\MessageFactory;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use App\Repository\DeviceRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Service\DeviceService;
use App\Service\TopicService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;


/**
 * @method User|null getUser()
 */
#[Route('/conversations/', name: 'app_conversation_')]
class ConversationController extends AbstractController
{

    public function __construct(
        private readonly Authorization          $authorization,
        private readonly ConversationRepository $conversationRepository,
        private readonly ConversationFactory    $conversationFactory,
        private readonly DeviceRepository       $deviceRepository,
        private readonly Discovery              $discovery,
        private readonly LoggerInterface        $logger,
        private readonly MessageFactory         $messageFactory,
        private readonly MessageRepository      $messageRepository,
        private readonly TopicService           $topicService,
    )
    {
    }

    #[Route('all', name: 'show_all')]
    #[IsGranted('ROLE_USER')]
    public function show(): Response
    {
        $conversations = $this->conversationRepository->findAllByUser($this->getUser());

        return $this->render('conversation/show.html.twig', [
            'conversations' => $conversations
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('send-message-new/{slugDevice}', name: 'send_new')]
    #[IsGranted('ROLE_USER')]
    public function sendNew(Request $request, string $slugDevice): Response
    {
        $device = $this->deviceRepository->findOneBy(['slug' => $slugDevice]);
        if(!$device){
            $this->logger->error("Device slug: $slugDevice not found ");
            throw new \Exception("Appareil n'existe pas", Response::HTTP_NOT_FOUND);
        }
        if($this->getUser() === $device->getUser()){
            $this->logger->error("");
            throw new \Exception("L'appareil vous appartient", Response::HTTP_NOT_FOUND);
        }
        $sender = $this->getUser();
        $recipient = $device->getUser();

        $conversations = $this->conversationRepository->findOneByUsers($sender, $recipient);
        if($conversations){
            dd('faire une redirection car une conversation existe deja');
        }


        $form = $this->createForm(MessageType::class, new Message());

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $content = $form->get('content')->getData();

            $conversations = $this->conversationFactory->create($sender, $recipient);

            $this->messageFactory->create($sender, $content , $conversations);

            $this->addFlash('success', 'Message envoyé avec succès');
        }

        return $this->render('conversation/send.html.twig', [
            'conversations' => $conversations,
            'form' => $form->createView()
        ]);
    }

    #[Route('message/{slugConversation}', name: 'message_conversation_slug')]
    #[IsGranted('ROLE_USER')]
    public function messagesConversation(Request $request, string $slugConversation): Response{

        $messages = $this->messageRepository->findOneBySlugConversation($slugConversation);

        $currentConversation = $this->conversationRepository->findOneBy(['slug' => $slugConversation]);

        $allConversations = $this->conversationRepository->findAllByUser($this->getUser());

        $topic = $this->topicService->getTopicUrl($currentConversation);

        $this->discovery->addLink($request);

        $this->authorization->setCookie($request, [$topic]);

        return $this->render('conversation/show.html.twig', [
            'messages' => $messages,
            'slugConversation' => $slugConversation,
            'conversations' => $allConversations,
            'currentConversation' => $currentConversation,
            'topic' => $topic
        ]);
    }


    #[Route('users/{recipient}', name: 'app_message_')]
    #[IsGranted('ROLE_USER')]
    public function shows(?User $recipient): Response
    {
        $sender = $this->getUser();

        $conversation = $this->conversationRepository->findOneByUsers($sender, $recipient);

        if(!$conversation) {
            $conversation = $this->conversationFactory->create($sender, $recipient);
        }

        return $this->render('conversation/index.html.twig', [
            'conversation' => $conversation
        ]);
    }

}

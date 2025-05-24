<?php

namespace App\Controller;

use App\DTO\CreateMessage;
use App\Entity\Message;
use App\Entity\User;
use App\Factory\MessageFactory;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @method User|null getUser()
 */
#[Route('/messages/', name: 'app_message_')]
class MessageController extends AbstractController
{

    public function __construct(
        private readonly ConversationRepository $conversationRepository,
        private readonly MessageFactory         $messageFactory,
    )
    {}
    #[Route('delete/{id}', name: 'delete')]
    #[isGranted('ROLE_USER')]
    public function deleteMessage(Request $request, Message $message): Response {

        $this->denyAccessUnlessGranted('delete', $message);

        $referer = $request->headers->get('referer');

        $this->messageFactory->delete($this->getUser(), $message);

        $this->addFlash('success', 'Message supprimé avec succès !');

        return $this->redirect($referer ?? $this->generateUrl('app_home'));
    }

    #[Route('new-message', name: 'new', methods: ['POST'], options:["expose" =>true])]
    #[IsGranted('ROLE_USER')]
    public function createMessage(#[MapRequestPayload] CreateMessage $payload): Response{

        $sender = $this->getUser();

        $conversation = $this->conversationRepository->findOneBy(['slug' => $payload->slugConversation]);

        $message = $this->messageFactory->create($sender, $payload->content , $conversation);

        return $this->render('conversation/messages.html.twig', ['message' => $message]);


//        $data = [
//            'author_id' => $message->getAuthor()->getId(),
//            'content' => $message->getContent(),
//        ];

//        dd($message);
//
//
//        $update = new Update(
//            topics: $this->topicService->getTopicUrl($conversation),
//            data: json_encode($data),
//            private: true
//        );
//
//        $this->hub->publish($update);
//
//        return new Response('', Response::HTTP_CREATED);

    }
}

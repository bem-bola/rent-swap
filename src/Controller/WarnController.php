<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Factory\WarnMessageFactory;
use App\Repository\WarnMessageRepository;
use App\Service\LoggerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method User|null getUser()
 */
#[Route('/warn/', name: 'app_warn_')]
final class WarnController extends AbstractController
{

    public function __construct(
        private readonly LoggerService          $loggerService,
        private readonly WarnMessageFactory     $warnMessageFactory,
        private readonly WarnMessageRepository  $warnMessageRepository,
    ){}

    #[Route('message/{id}', name: 'message')]
    #[isGranted('ROLE_USER')]
    public function message(Request $request, Message $message): Response
    {
        $referer = $request->headers->get('referer');

        $warnExiste = $this->warnMessageRepository->findByInformantMessage($this->getUser(), $message);

        if(!$warnExiste){
            $this->addFlash('error', "Message déjà signalé");
            $this->loggerService->write('error', sprintf("warn message exist. Message: %s", $message->getId()), Response::HTTP_CONFLICT, $this->getUser());
            $this->redirect($referer ?? $this->generateUrl('app_home'));
        }
        $this->warnMessageFactory->create($this->getUser(), $message);

        $this->addFlash('success', 'Message signalé avec succès !');

        return $this->redirect($referer ?? $this->generateUrl('app_home'));
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\Constances;
use App\Service\JWTService;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/register', name: 'app_register')]
class RegistrationController extends AbstractController
{

    public function __construct(
        private readonly JWTService                  $JWTService,
        private readonly SendEmailService            $sendEmailService,
        private readonly UserFactory                 $userFactory,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserRepository              $userRepository,
    ){

    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/', name: '')]
    public function register(Request $request, Security $security): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $password = $this->userPasswordHasher->hashPassword($user, $plainPassword);

            $this->userFactory->createByUser($user, $password);

            $token = $this->JWTService->generate(['user_id' => $user->getId()]);

            $this->sendEmailService->send(
                $this->getParameter('adressEmailNoReply'),
                $user->getEmail(),
                'Activation de votre compte sur Reusiix',
                'register',
                compact('token', 'user')
            );

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verif/{token}', name: '_verify_user')]
    public function verifUser(string $token): RedirectResponse
    {
        if($this->JWTService->isValid($token) &&
            !$this->JWTService->isExpired($token) &&
            $this->JWTService->check($token)){

            // Le token est valide
            // On récupère les données (payload)
            $payload = $this->JWTService->getPayload($token);

            // On récupère le user
            $user = $this->userRepository->find($payload['user_id']);

            // On vérifie qu'on a bien un user et qu'il n'est pas déjà activé
            if($user && !$user->isVerified()){
               $this->userFactory->updateStatus($user, Constances::VALIDED, $user);
                return $this->redirectToRoute('app_home');
            }
        }

        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }
}

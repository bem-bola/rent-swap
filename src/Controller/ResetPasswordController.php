<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Form\ChangePasswordForm;
use App\Form\ResetPasswordRequestForm;
use App\Repository\UserRepository;
use App\Service\LoggerService;
use App\Service\SendEmailService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @method User|null getUser()
 */
#[Route('/reset-password', name: 'app_reset_password_')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private readonly EntityManagerInterface         $entityManager,
        private readonly PasswordHasherFactoryInterface $hasherFactory,
        private readonly ResetPasswordHelperInterface   $resetPasswordHelper,
        private readonly SendEmailService               $sendEmailService,
        private readonly UserFactory                    $userFactory,
        private readonly UserRepository                 $userRepository,
        private readonly UserService                    $userService,
        private readonly LoggerService                  $loggerService
    ) {
    }

    /**
     * Affiche et traite le formulaire de demande de réinitialisation de mot de passe.
     * @throws TransportExceptionInterface
     */
    #[Route('', name: 'forgot_request')]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ResetPasswordRequestForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $email */
            $email = $form->get('email')->getData();

            return $this->processSendingPasswordResetEmail($email);
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form,
        ]);
    }

    /**
     * Valide et traite l’URL de réinitialisation cliquée par l’utilisateur dans l’email.
     */
    #[Route('/reset/{token}', name: 'reset')]
    public function reset(Request $request, TranslatorInterface $translator, ?string $token = null): Response
    {

        if ($token) {
            // On stocke le token en session et on le retire de l’URL, pour éviter qu’il ne fuite via des scripts tiers.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password_reset');
        }

        $token = $this->getTokenFromSession();

        if (null === $token) {
            throw $this->createNotFoundException('Aucun jeton de réinitialisation trouvé dans l’URL ou la session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {

            $this->loggerService->write('error', $e->getMessage());

            $this->addFlash('error', "Il y a eu un problème de validation de votre demande de réinitialisation de mot de passe.\nVeuillez réessayer");

            return $this->redirectToRoute('app_reset_password_reset_forgot_request');
        }

        // Le token est valide, on permet à l'utilisateur de changer son mot de passe.
        $form = $this->createForm(ChangePasswordForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $save = $this->userFactory->updatePassword($user, $form->get('plainPassword')->getData());

            if($save === false){
                $this->addFlash('error', 'Le nouveau mot de passe a déjà été utilisé récemment.');
                return $this->redirectToRoute('app_reset_password_reset', ['token' => $token]);
            }
            // Le token de réinitialisation ne doit être utilisé qu’une seule fo©is, on le supprime.
            $this->resetPasswordHelper->removeResetRequest($token);
            // Nettoyage de la session après le changement de mot de passe.
            $this->cleanSessionAfterReset();

            $this->addFlash('success', 'Mot de passe modifié avec succès');

            $this->sendEmailService->send(
                $this->getParameter('adressEmailNoReply'),
                $user->getEmail(),
                'Mot de passe modifié avec succès',
                'reset_password',
                compact('user')
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function processSendingPasswordResetEmail(string $emailFormData, string $pathname = 'app_login'): RedirectResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $emailFormData,]);

        // Ne pas révéler si un compte utilisateur a été trouvé ou non.
        if (!$user) {
            $this->addFlash('success', "Si un compte correspondant à votre adresse e-mail existe, un e-mail contenant un lien pour réinitialiser votre mot de passe vient d'être envoyé.");
            $this->loggerService->write('error', "User avec l'adresse email $emailFormData n'existe pas");
            return $this->redirectToRoute($pathname);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('success', "Si un compte correspondant à votre adresse e-mail existe, un e-mail contenant un lien pour réinitialiser votre mot de passe vient d'être envoyé.");
            $this->loggerService->write('error', $e->getMessage(), null, $user);
            return $this->redirectToRoute($pathname);

        }

        $this->addFlash('success', "Si un compte correspondant à votre adresse e-mail existe, un e-mail contenant un lien pour réinitialiser votre mot de passe vient d'être envoyé.");

        $this->sendEmailService->send(
            $this->getParameter('adressEmailNoReply'),
            $user->getEmail(),
            'Votre demande de réinitialisation de mot de passe',
            'reset_password',
            compact('resetToken')
        );


        // Stocke l’objet token en session pour la récupération dans la route check-email.
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute($pathname);
    }


    /**
     * @throws TransportExceptionInterface
     */
    public function resetPasswordByProfile(User $user, string $plainPassword, string $pathname): RedirectResponse
    {
        $passwordIsValid = $this->userService->checkPassword($user, $plainPassword);

        if(!$passwordIsValid){
            $this->addFlash('error', 'Mot de passe invalide.');
            return $this->redirectToRoute('app_user_home');
        }

        return $this->processSendingPasswordResetEmail($user->getEmail(), $pathname);
    }
}

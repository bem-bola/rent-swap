<?php

namespace App\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\Constances;
use App\Service\JWTService;
use App\Service\LoggerService;
use App\Service\SendEmailService;
use App\Service\UserService;
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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

/**
 * @method User|null getUser()
 */
#[Route('/register', name: 'app_register')]
class RegistrationController extends AbstractController
{

    public function __construct(
        private readonly LoggerService                  $loggerService,
        private readonly ResetPasswordHelperInterface   $resetPasswordHelper,
        private readonly UserFactory                    $userFactory,
        private readonly UserPasswordHasherInterface    $userPasswordHasher,
        private readonly UserService                    $userService
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

            try {
                $this->userService->validUser($user);

                $this->addFlash('success', "Un mail de confirmation vient de vous être envoyé.");

            } catch (ResetPasswordExceptionInterface $e) {
                $this->addFlash('success', "Un mail de confirmation vient de vous être envoyé.");
                $this->loggerService->write('error', $e->getMessage(), null, $user);
                return $this->redirectToRoute('app_home');

            }

            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * @throws ResetPasswordExceptionInterface
     */
    #[Route('/verif/{token}', name: '_verify_user')]
    public function verifUser(?string $token = null): RedirectResponse
    {
        if($token !== null){
            try{
                $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
                // Le token de réinitialisation ne doit être utilisé qu’une seule fo©is, on le supprime.
                $this->resetPasswordHelper->removeResetRequest($token);
                // On vérifie qu'on a bien un user et qu'il n'est pas déjà activé
                if($user && !$user->isVerified()){
                    $this->userFactory->updateStatus($user, Constances::VALIDED, $user);
                    $this->addFlash('success', 'Compte validé avec succès');
                    return $this->redirectToRoute('app_home');
                }
            }catch(ResetPasswordExceptionInterface $e){
                $this->addFlash('error', "Token invalid");
                $this->loggerService->write('error', $e->getMessage());
            }catch(\Exception $e){
                $this->addFlash('error', "Une erreurs'est produite ");
                $this->loggerService->write('error', $e->getMessage());

            }
            return $this->redirectToRoute('app_home');
        }

        $this->addFlash('error', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/send-maile-valid', name: '_send_mail_valid')]
    #[isGranted('ROLE_USER')]
    public function verifUserMail(){

        try {
            $this->userService->validUser($this->getUser());

            $this->addFlash('success', 'Un mail de confirmation vient de vous être envoyé.');

        } catch (ResetPasswordExceptionInterface|TransportExceptionInterface $e) {
            $this->addFlash('error', "Une erreur s'est produite veuillez réessayer plus tard !.");
            $this->loggerService->write('error', $e->getMessage(), null, $this->getUser());
        }

        return $this->redirectToRoute('app_user_home');

    }
}

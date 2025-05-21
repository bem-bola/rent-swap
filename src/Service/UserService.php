<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Device;
use App\Entity\User;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class UserService
{

    public function __construct(
        private readonly ParameterBagInterface          $parameterBag,
        private readonly LoggerService                  $loggerService,
        private readonly PasswordHasherFactoryInterface $hasherFactory,
        private readonly ResetPasswordHelperInterface   $resetPasswordHelper,
        private readonly SendEmailService               $sendEmailService,
    )
    {}

    /**
     * @param User $user
     * @param string $plainPassword
     * @return bool
     */
    public function isReusedPassword(User $user, string $plainPassword): bool
    {
        $hasher = $this->hasherFactory->getPasswordHasher($user);

        // Vérifie si le mot de passe correspond à l'actuel
        if ($hasher->verify($user->getPassword(), $plainPassword)) {
            $this->loggerService->write('warning', 'Le nouveau mot de passe est identique à l’actuel.');
            return false;
        }

        // Vérifie contre les anciens mots de passe (s'ils existent)
        foreach ($user->getOldPasswords() ?? [] as $oldHash) {
            if ($hasher->verify($oldHash, $plainPassword)) {
                $this->loggerService->write('warning', 'Le nouveau mot de passe a déjà été utilisé récemment.');
                return false;
            }
        }

        return true;
    }

    public function checkPassword(User $user, string $plainPassword): bool{
        $hasher = $this->hasherFactory->getPasswordHasher($user);
        return $hasher->verify($user->getPassword(), $plainPassword);
    }

    /**
     * @throws ResetPasswordExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function validUser(User $user): void
    {
        $token = $this->resetPasswordHelper->generateResetToken($user);

        $this->sendEmailService->send(
            $this->parameterBag->get('adressEmailNoReply'),
            $user->getEmail(),
            'Activation de votre compte sur Reusiix',
            'register',
            compact('token', 'user')
        );

    }

}

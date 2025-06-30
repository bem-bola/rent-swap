<?php

namespace App\Service;

use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class TwoFaCodeMailerService implements AuthCodeMailerInterface
{

    public function __construct(
        private readonly string $adressEmailNoReply,
        private readonly SendEmailService $sendEmailService,
    )
    {}

    /**
     * @throws TransportExceptionInterface
     */
    public function sendAuthCode(TwoFactorInterface $user): void
    {
        $code = $user->getEmailAuthCode();

        $this->sendEmailService->send(
            $this->adressEmailNoReply,
            $user->getEmail(),
            'Équipe des comptes Reusiix',
            '2fa_email',
            compact('user', 'code')
        );

    }
}
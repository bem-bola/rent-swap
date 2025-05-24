<?php

namespace App\Security;

use App\Entity\Message;
use App\Entity\User;
use App\Service\LoggerService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class MessageVoter extends Voter
{
    public function __construct(
        private readonly  LoggerService $loggerService,
        private readonly Security       $security,
    ) {}
    const DELETE = 'delete';
    const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE, self::EDIT])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Message) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            $this->loggerService->write('error', 'Object doit être un utilisateur');
            return false;
        }

        if ($this->security->isGranted('ROLE_MODERATOR')) {
            return true;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Message $message */
        $message = $subject;

        return match($attribute) {
            self::DELETE, self::EDIT => $this->canEditDelete($message, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEditDelete(Message $message, User $user): bool
    {
        return $user === $message->getAuthor();
    }
}
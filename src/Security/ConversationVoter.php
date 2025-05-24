<?php

namespace App\Security;

use App\Entity\Conversation;
use App\Entity\Device;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class ConversationVoter extends Voter
{
    public function __construct(
    ) {
    }
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const CREATE = 'create';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::CREATE])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Conversation) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Conversation $conversation */
        $conversation = $subject;

        return match($attribute) {
            self::VIEW, self::CREATE => $this->canEditView($conversation, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEditView(Conversation $conversation, User $user): bool
    {
        $users = $conversation->getUsers()->toArray();
        return in_array($user, $users);
    }
}
<?php

namespace App\Security;

use App\Entity\Device;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class DeviceVoter extends Voter
{
    public function __construct(
        private readonly Security $security,
    ) {
    }
    // these strings are just invented: you can use anything
    const VIEW = 'view';
    const EDIT = 'edit';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Device) {
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

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        // you know $subject is a Post object, thanks to `supports()`
        /** @var Device $device */
        $device = $subject;

        return match($attribute) {
            self::VIEW, self::EDIT => $this->canEditView($device, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function canEditView(Device $device, User $user): bool
    {
        return $user === $device->getUser();
    }
}
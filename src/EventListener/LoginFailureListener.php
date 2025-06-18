<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginFailureListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [ LoginFailureEvent::class => 'onLoginFailure'];
    }
    public function onLoginFailure(LoginFailureEvent $event): void
    {
        // Attente de 2 secondes
        sleep(2);
    }
}

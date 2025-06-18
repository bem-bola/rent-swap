<?php

namespace App\EventListener;

use App\Service\Constances;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedToNotFoundListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof AccessDeniedException) {
            // Remplacer par une 404
            $notFoundException = new NotFoundHttpException('Page non trouvée.');
            $event->setThrowable($notFoundException);
        }
    }
}

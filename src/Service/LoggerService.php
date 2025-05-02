<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Reservation;
use App\Entity\Device;
use App\Entity\ReservationStatus;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Array_;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class LoggerService
{

    const TYPE = ['error', 'debug', 'info', 'warning'];

    public function __construct(
        private readonly LoggerInterface    $logger,
        private readonly RequestStack       $requestStack
    )
    {}


    /**
     * @param string $type
     * @param string $message
     * @param int|null $statusCode
     * @param User|null $user
     * @return void
     */
    public function write(string $type, string $message, ?int $statusCode = null, ?User $user = null): void
    {
        $request = $this->requestStack->getCurrentRequest();
        // Adresse ip
        $ipClient = $request?->getClientIp();
        // User
        $userId = $user?->getId();
        // verififier si la type
        if(in_array($type, Constances::ARRAY_LEVEL_LOG)){
            $message = $this->logger->{$type}(sprintf("MESSAGE: %s - USER: %s - CODE: %s - IP ADRESS: %s - CLASS: %s - PATH: %s",
                $message, $userId, $statusCode, $ipClient, $this->getCallerClass(), $request->getPathInfo()
            ));
        }else{
            $this->logger->error(sprintf("MESSAGE: % - USER: %s - CODE: %s - IP ADRESS: %s - CLASS: %s - PATH: %s",
            "Le type $type n'est pas défini", $userId, $statusCode, $ipClient, $this->getCallerClass(), $request->getPathInfo()));
        }

    }

    /**
     * @return string
     */
    public function getCallerClass(): string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

        if (isset($trace[2]['class'])) {
            return $trace[2]['class'];
        }

        return 'unknown';
    }

}

<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Device;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageStandardService
{

    public function __construct(
        private readonly LoggerService $loggerService
    )
    {}

    /**
     * @param string $status
     * @param string $title
     * @param string|null $motif
     * @return string
     * @throws \Exception
     */
    public function mailStandardStatusDevice(string $status, string $title, string $motif = null): string
    {
        if($status === Constances::VALIDED) return sprintf("Bonne nouvelle ! Votre annonce intitulée « %s » a été validée et est désormais visible sur notre site.", $title);
        if($status === Constances::REJECTED) return sprintf("Votre annonce intitulée « %s » a été rejetée pour motif suivant: %s.", $title, $motif);
        if($status === Constances::DELETED) return sprintf("Votre annonce intitulée « %s » a été supprimée pour motif suivant: %s.", $title, $motif);

        $this->loggerService->write('error', 'Une erreur est survenue status inconnue');
        throw new \Exception('Une erreur est survenue status inconnue');
    }
}

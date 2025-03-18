<?php

namespace App\Controller;

use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation', name: 'app_reservation_')]
final class ReservationController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/create', name: 'app_reservation_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, ReservationService $reservationService): \Symfony\Component\HttpFoundation\JsonResponse
    {
        return $reservationService->create($request, $this->getUser());
    }

}

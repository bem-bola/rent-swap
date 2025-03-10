<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Device;
use App\Entity\ReservationStatus;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ReservationService
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    private DeviceService $deviceService;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, DeviceService $deviceService)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->deviceService = $deviceService;
    }

    /**
     * @throws \Exception
     */
    public function create(Request $request, UserInterface $user): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $slug = $data['slug'] ?? null;
        $startDate = isset($data['startDate']) ? new \DateTime($data['startDate']) : null;
        $endDate = isset($data['endDate']) ? new \DateTime($data['endDate']) : null;

        if (!$slug || !$startDate || !$endDate) {
            $this->logger->error("Invalid data");
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $device = $this->entityManager->getRepository(Device::class)->findOneBy(['slug' => $slug]);

        // verifier si device existe
        if (!$device) {
            $this->logger->error("Device slug: $slug not found");
            return new JsonResponse(['error' => 'Appareil non connu'], Response::HTTP_NOT_FOUND);
        }

        // Vérifier si le l'appareil appartient à l'utilisateur
        if(!$this->deviceService->isUserValid($device, $user)) {
            $this->logger->error("Device slug: $slug not found");
            return new JsonResponse(['error' => "Vous ne pouvez pas réservé votre propre appareil"], Response::HTTP_NOT_FOUND);
        }

        // Vérifier le statut du device
        if(!$device->isStatusValid()){
            $this->logger->error("Device not valid");
            return new JsonResponse(['error' => "Cet appareil n'est pas disponible"], Response::HTTP_NOT_FOUND);
        }

        $reservationDate = $this->entityManager->getRepository(Reservation::class)->getOneReservationByDates($device, $startDate, $endDate);

        if(!$reservationDate){
            $this->logger->error("Appareil indisponible");
            return new JsonResponse(['error' => "Cet appareil n'est pas disponible à ces dates"], Response::HTTP_NOT_FOUND);
        }

        if(!$user){
            $this->logger->error("Cet appareil n'est pas disponible");
            return new JsonResponse(['error' => "Cet appareil n'est pas disponible à ces dates"], Response::HTTP_NOT_FOUND);
        }

        // Création de la réservation
        $reservation = new Reservation();
        $reservation->setUser($user);
        $reservation->setDevice($device);
        $reservation->setPrice($device->getPrice());
        $reservation->setCreated(new \DateTime());
        $reservation->setEnded($endDate);
        $reservation->setStated($startDate);

        // Définir le statut de réservation
        $reservation->setStatus(Constances::PENDING);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        $this->logger->info("Device {$device->getId()} reserved by user {$user->getId()}");

        return new JsonResponse(['message' => 'Reservation successful'], JsonResponse::HTTP_CREATED);
    }


//    public function update(Request $request, int $idDevice): JsonResponse{
//
//    }

}

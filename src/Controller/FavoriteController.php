<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\Favorite;
use App\Service\DeviceService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/favorite', name: 'app_favorite_')]
final class FavoriteController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;


    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    #[Route('/add-remove/{slug}', name: 'add_remove', options: ["expose" => true])]
    #[IsGranted('ROLE_USER')]
    public function addRemove(Request $request, EntityManagerInterface $em, string $slug): Response
    {
        if(!$request->isXmlHttpRequest()){

            $device = $this->entityManager->getRepository(Device::class)->findOneBy(['slug' => $slug]);

            if(!$device) {
                $this->logger->error("Device slug: $slug not found");
                return new JsonResponse(['error' => 'Appareil non connu'], Response::HTTP_NOT_FOUND);
            }
            $favorite = $this->entityManager->getRepository(Favorite::class)->findOneBy(['user' => $this->getUser(), 'device' => $device]);
            if(!$favorite) {
                $favorite = new Favorite();
                $favorite->setUser($this->getUser());
                $favorite->setDevice($device);
                $favorite->setIsFavorite(true);
            }else{
                $favorite->setIsFavorite(!$favorite->getIsFavorite());
            }
            $this->entityManager->persist($favorite);
            $this->entityManager->flush();

            return $this->json(['favorite' => $favorite->getIsFavorite()],Response::HTTP_OK);
        }

        return $this->json([], Response::HTTP_NOT_FOUND);
    }
}

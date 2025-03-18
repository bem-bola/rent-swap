<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Service\HttpClientService;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[Route('/device', name: 'app_device_')]
class DeviceController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = $request->query->get('category');

        return $this->render('device/search.html.twig', [
            'devices' =>  $entityManager->getRepository(Device::class)->findBy([], [], 30),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/show/{slug}', name: 'show')]
    public function view(Request $request, string $slug, EntityManagerInterface $entityManager, HttpClientService $httpClientService): Response
    {

        $device = $entityManager->getRepository(Device::class)->findOneBy(['slug' => $slug]);
        $devicesSimilar = $entityManager->getRepository(Device::class)->findSimilarDevices($device);
        $devicesUser = $entityManager->getRepository(Device::class)->findBy(['user' => $device->getUser()]);

        $coordonates = $httpClientService->request(
            sprintf($this->getParameter('urlNominatim'), urlencode($device->getLocation())),
        );
        return $this->render('device/show.html.twig', [
           'device' => $device,
            'devicesSimilar' => $devicesSimilar,
            'devicesUser' => $devicesUser,
            'coordonates' => ['lat' => $coordonates[0]['lat'], 'lon' => $coordonates[0]['lon']],
        ]);
    }
}

<?php

namespace App\Controller;

use App\DTO\SearchDevice;
use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Entity\Favorite;
use App\Repository\CategoryRepository;
use App\Repository\DeviceRepository;
use App\Service\HttpClientService;
use App\Service\PaginatorService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[Route('/device', name: 'app_device_')]
class DeviceController extends AbstractController
{

    public function __construct(
        private readonly CategoryRepository     $categoryRepository,
        private readonly DeviceRepository       $deviceRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface        $logger)
    {}
    #[Route('/search', name: 'search')]
    public function search(Request $request): Response
    {
        $sort = $request->get('orderby');
        return $this->render('device/search.html.twig', [
            'datas' => $this->deviceRepository->findByFilters($request->query->all()),
            'categories' => $this->categoryRepository->findBy([], ['name' => 'ASC']),
            'sortPrice' => $sort != null ? $sort['price'] : null,
            'filters' => $request->get('filters', []),
        ]);
    }

    #[Route('/search/by-form', name: 'search_by_form')]
    public function searchByForm(Request $request): Response
    {
        if(!$request->headers->get('HX-Request')){
            return $this->redirectToRoute('app_device_search', $request->query->all());
        }
        return $this->render('_partial/device/results_search.html.twig', [
            'datas' => $this->deviceRepository->findByFilters($request->query->all()),
            'sortPrice' => $request->get('orderby') != null ? $request->get('orderby')['price'] : null,
            'filters' => $request->get('filters', [])
        ]);
    }
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     */
    #[Route('/show/{slug}', name: 'show')]
    public function view(Request $request, string $slug, EntityManagerInterface $entityManager, HttpClientService $httpClientService): Response
    {

        $device = $entityManager->getRepository(Device::class)->findOneBy(['slug' => $slug]);

        if(!$device){
            $this->logger->error("Device slug: $slug not found");
            throw new Exception("Cette page n'existe pas", Response::HTTP_NOT_FOUND);
        }
        if($this->getUser()){
            $favorite = $this->entityManager->getRepository(Favorite::class)->findOneBy(['user' => $this->getUser(), 'device' => $device]);
        }
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
            'favorite' => $favorite ?? null,
        ]);
    }
}


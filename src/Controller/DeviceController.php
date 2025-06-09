<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\Favorite;
use App\Entity\Message;
use App\Entity\User;
use App\Factory\ConversationFactory;
use App\Factory\MessageFactory;
use App\Form\MessageType;
use App\Repository\CategoryRepository;
use App\Repository\DeviceRepository;
use App\Service\DeviceService;
use App\Service\HttpClientService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * @method User|null getUser()
 */
#[Route('/device', name: 'app_device_')]
class DeviceController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository     $categoryRepository,
        private readonly ConversationFactory    $conversationFactory,
        private readonly DeviceRepository       $deviceRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageFactory         $messageFactory,
        private readonly MessageService         $messageService,
        private readonly LoggerInterface        $logger)
    {}

    #[Route('/index', name: 'index')]
    public function index(): Response
    {

        return $this->render('device/search.html.twig', [
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/search', name: 'search')]
    public function search(Request $request, ?string $slugDevice = null): Response
    {
        $sort = $request->get('orderby');
        return $this->render('device/search.html.twig', [
            'datas' => $this->deviceRepository->findByFilters($request->query->all()),
            'categories' => $this->categoryRepository->findBy([], ['name' => 'ASC']),
            'sortPrice' => $sort != null ? $sort['price'] : null,
            'filters' => $request->get('filters', []),
        ]);
    }



    /**
     * @throws \Doctrine\DBAL\Exception
     */
    #[Route('/search/by-form', name: 'search_by_form')]
    public function searchByForm(Request $request): Response
    {
        if(!$request->headers->get('HX-Request')){
            return $this->redirectToRoute('app_device_search', $request->query->all());
        }
        return $this->render('_partial/device/results_search.html.twig', [
            'datas' => $this->deviceRepository->findByFilters($request->query->all()),
            'sortPrice' => $request->get('orderby') != null ? $request->get('orderby')['price'] : null,
            'filters' => $request->get('filters', []),
            'routeSearchName' => 'app_device_search_by_form'
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

        $form = $this->createForm(MessageType::class, $this->messageService->messageDefaultDevice($device));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $content = $form->get('content')->getData();

            $conversations = $this->conversationFactory->create($this->getUser(), $device->getUser(), $device);

            $this->messageFactory->create($this->getUser(), $content , $conversations);

            $this->addFlash('success', 'Message envoyé avec succès');

            return $this->redirectToRoute('app_device_show', ['slug' => $device->getSlug()]);

        }

        $coordonates = $httpClientService->request(
            sprintf($this->getParameter('urlNominatim'), urlencode($device->getLocation())),
        );
        return $this->render('device/show.html.twig', [
           'device' => $device,
            'devicesSimilar' => $devicesSimilar,
            'devicesUser' => $devicesUser,
            'coordonates' => ['lat' => $coordonates[0]['lat'], 'lon' => $coordonates[0]['lon']],
            'favorite' => $favorite ?? null,
            'form' => $form->createView(),
        ]);
    }
}


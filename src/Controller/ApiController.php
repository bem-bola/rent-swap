<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Service\HttpClientService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[Route('/api', name: 'app_api_')]
final class ApiController extends AbstractController
{
    public function __construct(private readonly HttpClientService $httpClientService)
    {}

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/city', name: 'city', options: ["expose" => true])]
    public function index(Request $request): Response
    {
        $name = $request->query->get('name');

        return $this->json($this->httpClientService->getCities($name), Response::HTTP_OK);
    }
}

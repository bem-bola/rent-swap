<?php

// src/Service/NominatimService.php
namespace App\Service;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClientService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly HttpClientInterface $httpClient,
        private readonly string $unsplashAccessKey,
        private readonly string $urlApiGeoGouv)
    {}

    /**
     * @param string $url
     * @param array $headers
     * @param string $method
     * @param array $options
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function request(string $url, array $options = [], array $headers = [], string $method = 'GET'): array{
        $response = $this->httpClient->request($method, $url, $options);
        return $response->toArray();
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function downloadAndSaveImageUnsPlash(string $targetPath): void
    {
        $options = [
            'query' => [
                'query' => 'technology',
                'client_id' => $this->unsplashAccessKey,
            ],
        ];
        $response = $this->request('https://api.unsplash.com/photos/random', $options);
        $imageUrl = $response['urls']['regular'];
        $imageContent = $this->httpClient->request('GET', $imageUrl)->getContent();
        file_put_contents($targetPath, $imageContent);
    }

    /**
     * Obtenir les coordonnées d'une ville avec Nominatim
     */
    public function getCoordinates(string $city): ?array
    {
        $url = sprintf('https://nominatim.openstreetmap.org/search?q=%s&format=json&addressdetails=1&limit=1', urlencode($city)
        );

        $headers = ['User-Agent' => 'YourAppName/1.0 (your_email@example.com)'];

        $data = $this->request($url, $headers);


        if (!empty($data)) {
            return [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon'],
            ];
        }

        return null;
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function getCities($name): array
    {
        $response = $this->httpClient->request(
            'GET',
            $this->urlApiGeoGouv,
            [
                'query' => [
                    'fields' => 'nom,code,codesPostaux',
                    'nom' => $name,
                ]
            ]
        );


        $results = [];

        foreach ($response->toArray() as $data){
            if(count($data['codesPostaux']) > 1) {
                foreach ($data['codesPostaux'] as $codePostal){
                    $results[]['name'] = sprintf("%s - %s", $data['nom'], $codePostal);
                }
            }else{
                $results[]['name'] = sprintf("%s - %s", $data['nom'], empty($data['codesPostaux']) ? null: $data['codesPostaux'][0]);
            }
        }
        return $results;
    }
}



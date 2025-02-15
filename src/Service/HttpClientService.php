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
    private HttpClientInterface $httpClient;

    private EntityManagerInterface $em;

    private string $unsplashAccessKey;

    private string $urlApiGeoGouv;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $em, string $urlApiGeoGouv, string $unsplashAccessKey)
    {
        $this->httpClient = $httpClient;
        $this->em =$em;
        $this->urlApiGeoGouv = $urlApiGeoGouv;
        $this->unsplashAccessKey = $unsplashAccessKey;
    }

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


    public function getCities($dep): array
    {

        https://geo.api.gouv.fr/departements/972/communes?fields=codesPostaux,nom&limit=2
        $response = $this->httpClient->request(
            'GET',
            "https://geo.api.gouv.fr/departements/$dep/communes",
            [
                'query' => [
                    'fields' => 'nom,code,codesPostaux',
                    'limit' => 10
                ]
            ]
        );

        foreach ($response->toArray() as $data){

            if(count($data['codesPostaux']) > 1) {

                foreach ($data['codesPostaux'] as $cc){
                    $city = new City();
                    $city->setName($data['nom']);
                    $city->setCodePostal($cc);

                    $this->em->persist($city);
                }

            }else{

                $city = new City();
                $city->setName($data['nom']);
                $city->setCodePostal(empty($data['codesPostaux']) ? null: $data['codesPostaux'][0]);
                $this->em->persist($city);

            }

    }

        $this->em->flush();



        return $response->toArray();
    }
}



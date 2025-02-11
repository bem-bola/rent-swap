<?php

// src/Service/NominatimService.php
namespace App\Service;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class HttpClientService
{
    private HttpClientInterface $httpClient;

    private EntityManagerInterface $em;

    private string $urlApiGeoGouv;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $em, string $urlApiGeoGouv)
    {
        $this->httpClient = $httpClient;
        $this->em =$em;
        $this->urlApiGeoGouv = $urlApiGeoGouv;
    }

    /**
     * @param string $url
     * @param array $headers
     * @param string $method
     * @param array $options
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function request(string $url, array $options = [], array $headers = [], string $method = 'GET'): array{
        $response = $this->httpClient->request($method, $url, $options);
        return $response->toArray();
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

        dd($response->toArray());


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



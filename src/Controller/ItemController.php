<?php

namespace App\Controller;

use App\Entity\City;
use App\Service\HttpClientService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/item', name: 'app_item_')]
class ItemController extends AbstractController
{
    #[Route('/view/{slug}', name: 'view')]
    public function view(string $slug, HttpClientService $httpClientService): Response
    {
        $data = [
            [
                'image' => 'https://picsum.photos/300/300',
                'title' => 'img-1'
            ],
            [
                'image' => 'https://picsum.photos/700/300',
                'title' => 'img-3'
            ],
            [
                'image' => 'https://picsum.photos/400/300',
                'title' => 'img-2'
            ],
            [
                'image' => 'https://picsum.photos/600/450',
                'title' => 'img-4'
            ],
            [
                'image' => 'https://picsum.photos/700/300',
                'title' => 'img-3'
            ],
            [
                'image' => 'https://picsum.photos/400/600',
                'title' => 'img-4'
            ],
            [
                'image' => 'https://picsum.photos/700/300',
                'title' => 'img-3'
            ],

        ];

        $ville = 'Cergy';
//        $coordonates = $httpClientService->request(
//            sprintf($this->getParameter('urlNominatim'), urlencode($ville)),
//            ['User-Agent' => $this->getParameter('nameProject')]
//
//        );


        return $this->render('item/view.html.twig', [
            'datas' => $data,
//            'coordonates' => !empty($coordonates) ? $coordonates[0] : [],
            'coordonates' =>  [],

        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(HttpClientService $httpClientService, Request $request): Response
    {
        $data = [
            [
                'image' => 'https://picsum.photos/300/300',
                'title' => 'img-1'
            ],
            [
                'image' => 'https://picsum.photos/700/300',
                'title' => 'img-3'
            ],
            [
                'image' => 'https://picsum.photos/400/300',
                'title' => 'img-2'
            ],
            [
                'image' => 'https://picsum.photos/600/450',
                'title' => 'img-4'
            ],
            [
                'image' => 'https://picsum.photos/700/300',
                'title' => 'img-3'
            ],
            [
                'image' => 'https://picsum.photos/400/600',
                'title' => 'img-4'
            ],
            [
                'image' => 'https://picsum.photos/700/300',
                'title' => 'img-3'
            ],

        ];


        return $this->render('item/search.html.twig', [
            'datas' => $data,
            'coordonates' => !empty($coordonates) ? $coordonates[0] : [],
            'nbPage'  => 30,
            'currentPage'   => (int) $request->get('page', 1)
        ]);
    }

    #[Route('/city', name: 'city', options: ['expose' => true])]
    public function city(Request $request, HttpClientService $httpClientService): Response
    {


        $city = $request->get('city');
        $cities = $httpClientService->request(
            $this->getParameter('urlApiGeoGouv'),
            [
                'query' => [
                    'fields' => 'nom,code,codesPostaux,centre',
                    'nom' => $city,
                    'limit' => 10
                ]
            ]
        );

        return new JsonResponse([
            $this->renderView('_partial/item/cities.html.twig', [
                'cities' => $cities,
                'city'   => $city
            ])
        ]);
    }
}

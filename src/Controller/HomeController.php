<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\TypeUser;
use App\Service\HttpClientService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        return $this->render('home/index.html.twig', [
            'slides' => [
        ['text' => "Ça fait plus d'un an que je partage Youtube Famille tout marche comme sur des roulettes", 'alt' => 'Slide 1', 'auteur' => 'Bolain'],
        ['text' =>  "Super bonne initiative !!! Nous pouvons profiter de plusieurs abonnements à moinsde 10€/mois alors qu’en réalité un seul ", 'alt' => 'Slide 2', 'auteur' => 'Alain'],
        ['text' => "Merci au service client pour sa réactivité et sa gentillesse, mon problème a été réglé dans un temps record.  Je recommande qui va", 'alt' => 'Slide 3', 'auteur' => 'Messi']
        ],
            'categories' => $entityManager->getRepository(Category::class)->findAll()
        ]);
    }
}

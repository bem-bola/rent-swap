<?php

namespace App\Controller;


use App\Entity\Category;
use App\Entity\User;
use App\Service\HttpClientService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher,
    private readonly EntityManagerInterface $entityManager,)
    {}

    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {

//        $user = new User();
//        $user->setUsername('admin');
//        $user->setEmail('admin@test.com');
//        $user->setFirstname('admin');
//        $user->setLastname('admin');
//        $user->setCreated(new \DateTime());
//        $user->setIsVerified(true);
//
//        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password123!');
//        $user->setPassword($hashedPassword);
//
//        $this->entityManager->persist($user);
//
//        $this->entityManager->flush();


        return $this->render('home/index.html.twig', [
            'slides' => [
        ['text' => "Ça fait plus d'un an que je partage Youtube Famille tout marche comme sur des roulettes", 'alt' => 'Slide 1', 'auteur' => 'Bolain'],
        ['text' =>  "Super bonne initiative !!! Nous pouvons profiter de plusieurs abonnements à moinsde 10€/mois alors qu’en réalité un seul ", 'alt' => 'Slide 2', 'auteur' => 'Alain'],
        ['text' => "Merci au service client pour sa réactivité et sa gentillesse, mon problème letters été réglé dans un temps record.  Je recommande qui va", 'alt' => 'Slide 3', 'auteur' => 'Messi']
        ],
            'categories' => $entityManager->getRepository(Category::class)->findBy([], ['name' => 'ASC'], 10)
        ]);
    }
}

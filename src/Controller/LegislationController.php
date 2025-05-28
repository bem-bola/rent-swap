<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/legislation', name: 'app_legislation_')]
final class LegislationController extends AbstractController
{
    #[Route('/mentions-legales', name: 'mentions_legales')]
    public function mentions(): Response
    {
        return $this->render('legislation/mentions_legales.html.twig', [
        ]);
    }

    #[Route('/conditions-generales-de-vente-et-d-utilisation', name: 'cgvu')]
    public function cvgu(): Response
    {
        return $this->render('legislation/cgvu.html.twig', [
        ]);
    }
}

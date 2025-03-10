<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\DevicePicture;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/device', name: 'app_device_')]
class DeviceController extends AbstractController
{
    #[Route('/search', name: 'search')]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = $request->query->get('category');

        return $this->render('device/search.html.twig', [
            'devices' =>  $entityManager->getRepository(Device::class)->findBy([], [], 30),
        ]);
    }

  #[Route('/show/{slug}', name: 'show')]
    public function view(Request $request, string $slug, EntityManagerInterface $entityManager): Response
    {

        $device = $entityManager->getRepository(Device::class)->findOneBy(['slug' => $slug]);

        return $this->render('device/show.html.twig', [
           'device' => $device,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\User;
use App\Factory\DeviceFactory;
use App\Form\DeviceType;
use App\Repository\DeviceRepository;
use App\Repository\FavoriteRepository;
use App\Service\DeviceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method User|null getUser()
 */
#[Route(path: '/account/private', name: 'app_user_')]
#[isGranted('ROLE_USER')]
class UserController extends AbstractController
{

    public function __construct(
        private readonly DeviceFactory      $deviceFactory,
        private readonly DeviceRepository   $deviceRepository,
        private readonly FavoriteRepository $favoriteRepository,
        private readonly DeviceService      $deviceService,
    )
    {}

    #[Route(path: '/', name: 'home')]
    public function profile(): Response
    {
        return $this->render('security/profile.html.twig', [
            'devices' => $this->deviceRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route(path: '/devices', name: 'devices')]
    public function devices(Request $request): Response
    {
        $sort = $request->get('orderby');
        $view = $request->headers->get('HX-Request') ?
            '_partial/device/results_search.html.twig': 'security/device.html.twig';

        return $this->render($view, [
            'datas' => $this->deviceRepository->findByUser($request->query->all(), $this->getUser()),
            'sortPrice' => $sort != null ? $sort['price'] : null,
            'filters' => $request->get('filters', []),
            'routeSearchName' => 'app_user_devices'
        ]);
    }

    #[Route(path: '/favorites', name: 'favorites')]
    public function favorite(Request $request): Response
    {
        $sort = $request->get('orderby');

        $view = $request->headers->get('HX-Request') ?
            '_partial/device/results_search.html.twig': 'security/favorite.html.twig';

        return $this->render($view, [
            'datas' => $this->favoriteRepository->findByUser($request->query->all(), $this->getUser()),
            'sortPrice' => $sort != null ? $sort['price'] : null,
            'filters' => $request->get('filters', []),
            'routeSearchName' => 'app_user_favorites'
        ]);
    }

    #[Route(path: '/device/update/{slug}', name: 'update_device')]
    public function updateDevice(Request $request, Device $device): Response
    {
        try{
            $this->denyAccessUnlessGranted('edit', $device);
             // Recuperer le dernier parent enreigistre
            $lastVersionDevice = $this->deviceRepository->findLatestVersionByParent($device);

            $newDevice = $this->deviceFactory->initDeviceByDeviceId($lastVersionDevice);

            $form = $this->createForm(DeviceType::class, $newDevice);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $parent = $lastVersionDevice->getParent() ?? $lastVersionDevice;
                // On modifie le parent
                $this->deviceFactory->updateByDevice($lastVersionDevice);
                // récuperation des catégorie
                $categories = $request->get('categories');
                // Verifie s'il y a des modification ou pas en cas de modificatioin un nouveau device est créé sinon rien
                $diff = $this->deviceService->isEquivalentTo($lastVersionDevice, $newDevice, $categories);
                // Enregister ou pas nouveau device
                $this->deviceFactory->createWithCategory($newDevice, $parent, $categories, $diff);

            }
        }catch (AccessDeniedHttpException|\Exception $e){
            dd($e->getMessage(), $e->getCode(), 'renvoyer page 404 - acces refusé');
//            return $this->redirectToRoute('app_user_devices');
        }


        return $this->render('security/update_device.html.twig', [
           'device' => $lastVersionDevice,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/device/category/{slug}', name: 'category_device')]
    #[Route(path: '/device/category', name: 'category_device')]
    public function categoriesDevices(Request $request, ?Device $device): Response{

        return $this->render('_partial/device/category.html.twig', []);
    }

}

<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Entity\User;
use App\Factory\DeviceFactory;
use App\Factory\DevicePictureFactory;
use App\Factory\MediaFactory;
use App\Factory\UserFactory;
use App\Form\DeviceType;
use App\Form\UserType;
use App\Repository\DevicePictureRepository;
use App\Repository\DeviceRepository;
use App\Repository\FavoriteRepository;
use App\Service\Constances;
use App\Service\DeviceService;
use App\Service\LoggerService;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        private readonly DeviceFactory              $deviceFactory,
        private readonly DevicePictureFactory       $devicePictureFactory,
        private readonly DevicePictureRepository    $devicePictureRepository,
        private readonly DeviceRepository           $deviceRepository,
        private readonly DeviceService              $deviceService,
        private readonly EntityManagerInterface     $entityManager,
        private readonly FavoriteRepository         $favoriteRepository,
        private readonly LoggerService              $loggerService,
        private readonly MediaFactory               $mediaFactory,
        private readonly UploadFileService          $uploadFileService,
        private readonly UserFactory                $userFactory,
    )
    {}

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    #[Route(path: '/', name: 'home', options: ['expose' => true])]
    public function profile(Request $request): Response
    {
        $form = $this->createForm(UserType::class, $this->getUser());
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($form->getData());
            $this->entityManager->flush();

            $this->addFlash('success', "Vos informations ont été modifiées");
        }

        if($request->headers->get('HX-Request')) {

            try{
                $file = $request->files->get('file');

                $filename = $this->uploadFileService->uploadFile($file, 'avatars');

                $media = $this->mediaFactory->createByUser($file, $filename, $this->getUser());

                $this->userFactory->addAvatar($this->getUser(), $media);

                $this->addFlash('success', 'Avatar modifié avec succès');

            }catch (\Exception $e){
                $this->loggerService->write(Constances::LEVEL_ERROR, $e->getMessage(), null, $this->getUser());
            }


            return new JsonResponse([
                'url' => 'app_user_devices',
            ]);
        }

        return $this->render('security/profile.html.twig', [
            'data' => $this->deviceRepository->findByUser([], $this->getUser()),
            'form' => $form->createView(),
        ]);
    }

//    #[Route(path: '/', name: 'home')]
//    public function profile(): Response
//    {
//        return $this->render('security/profile.html.twig', [
//            'data' => $this->deviceRepository->findByUser([], $this->getUser()),
//        ]);
//    }

    /**
     * @param Request $request
     * @return Response
     */
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

    /**
     * @param Request $request
     * @return Response
     */
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

    /**
     * @param Request $request
     * @param Device $device
     * @return Response
     */
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
                // parent
                $parent = $lastVersionDevice->getParent() ?? $lastVersionDevice;
                // On modifie le parent
                $this->deviceFactory->updateByDevice($lastVersionDevice);
                // récuperation des catégorie
                $categories = $request->get('categories');

                $newDevice->setLocation($request->get('location'));

               $status = $this->deviceService->handleFormStatus($newDevice, $form);

               $newDevice->setStatus($status);

                // Verifie s'il y a des modification ou pas en cas de modificatioin un nouveau device est créé sinon rien
                $diff = $this->deviceService->isEquivalentTo($lastVersionDevice, $newDevice, $categories);

                // Enregister ou pas nouveau device
                $this->deviceFactory->createWithCategory($newDevice, $parent, $status, $categories, $diff);

                return $this->deviceService->redirect($form, ['slug' => $device->getSlug()], $this->getUser());
           }
        }catch (AccessDeniedHttpException $e){
            $this->loggerService->write(Constances::LEVEL_ERROR, $e->getMessage(), Response::HTTP_UNAUTHORIZED, $this->getUser());
            dd($e->getMessage(), $e->getCode(), 'renvoyer page 404 - acces refusé');
        }catch (\Exception $e){
            $this->loggerService->write(Constances::LEVEL_ERROR, $e->getMessage(), null, $this->getUser());
            $this->addFlash('error', "une erreur s'est produite lors de la modification de cette annonce");

            dd('renvoyer page 404 - acces refusé', $e->getMessage());
        }

        return $this->render('security/update_device.html.twig', [
           'device' => $lastVersionDevice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    #[Route(path: '/device/create', name: 'create_device')]
    public function createDevice(Request $request): Response{

        $device = new Device();

        $form = $this->createForm(DeviceType::class, $device, ['create' => true]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $categories = $request->get('categories');

            $location = $request->get('location');

            $status = $this->deviceService->handleFormStatus($device, $form);

            $this->deviceFactory->createWithCategory($device, null, $status, $categories, false, $location, $this->getUser());

            return $this->deviceService->redirect($form, ['slug' => $device->getSlug()], $this->getUser());
        }

        return $this->render('security/update_device.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route(path: '/device/update/status/{slug}/{status}', name: 'device_set_status')]
    public function setStatus(string $slug, string $status): Response{
        try{
            $device = $this->deviceRepository->findOneBy(['slug' => $slug]);

            $this->denyAccessUnlessGranted('edit', $device);

            $this->deviceFactory->setStatus($device, $status);

            if($status === Constances::DRAFT) $this->addFlash('success', 'Votre annonce a été sauvegardée avec succès');
            elseif($status === Constances::DELETED) $this->addFlash('success', 'Votre annonce a été supprimée avec succès');
            elseif($status === Constances::PENDING) $this->addFlash('success', 'Votre annonce a été sauvergardée et sera validée par notre équipe');

            return $this->redirectToRoute('app_user_devices');
        }catch (\Exception $e){
            $this->loggerService->write(Constances::LEVEL_ERROR, $e->getMessage(), null, $this->getUser());

            dd('page error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param Device $device
     * @return Response
     */
    #[Route(path: '/device/images/upload/{slug}', name: 'upload_image_device', options: ["expose" => true])]
    public function categoriesDevices(Request $request, Device $device): Response{

        try {

            $this->denyAccessUnlessGranted('edit', $device);

            $lastVersionDevice = $this->deviceRepository->findLatestVersionByParent($device);

            $parent = $lastVersionDevice->getParent() ? $lastVersionDevice->getParent() : $lastVersionDevice;

            if($request->headers->get('HX-Request')){

                $files = $request->files->get('files');

                foreach($files as $file){
                    $filename = $this->uploadFileService->uploadFile($file, 'devices');

                    if($filename) $this->devicePictureFactory->create($parent, $filename);
                }

                return new JsonResponse([
                    'url' => 'app_user_devices',
                ]);
            }


        }catch (AccessDeniedHttpException $e){
            $this->loggerService->write(Constances::LEVEL_ERROR, $e->getMessage(), Response::HTTP_UNAUTHORIZED, $this->getUser());
            dd($e->getMessage(), $e->getCode(), 'renvoyer page 404 - acces refusé');
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getCode(), 'renvoyer page 404 - acces refusé');
        }

        return $this->render('security/upload_image.html.twig', [
            'device' => $lastVersionDevice,
            'data' => $this->devicePictureRepository->findByParent($lastVersionDevice),
            'draft' => Constances::DRAFT,
            'pending' => Constances::PENDING,
            'delete' => Constances::DELETED
        ]);
    }

    /**
     * Get images by device
     * @param Request $request
     * @param string $slugDevice
     * @return Response
     * @throws \Exception
     */
    #[Route(path: '/device/images/{slugDevice}', name: 'devices_images', options: ["expose" => true])]
    public function deletePictureDevice(Request $request, string $slugDevice): Response
    {
        if ($request->headers->get('HX-Request')) {

            $device = $this->deviceRepository->findOneBy(['slug' => $slugDevice]);

            try {

                $this->denyAccessUnlessGranted('edit', $device);

            } catch (AccessDeniedHttpException $e){
                $this->loggerService->write(Constances::LEVEL_ERROR, $e->getMessage(), Response::HTTP_UNAUTHORIZED, $this->getUser());
                throw new AccessDeniedHttpException('page 404', null, Response::HTTP_NOT_FOUND);
            }catch (\Exception $e) {
                $this->loggerService->write(Constances::LEVEL_ERROR, $e->getMessage(), null, $this->getUser());
                throw new \Exception('Page 404',Response::HTTP_NOT_FOUND);
            }

            return $this->render('_partial/device/picture_device.html.twig', [
                'data' => $this->devicePictureRepository->findByParent($device, $request->query->all()),
                'device' => $device,
            ]);
        }

        return throw new \Exception('Page 404',Response::HTTP_NOT_FOUND);
    }



    /**
     * Upload image
     * @param Request $request
     * @param int $id
     * @param string $slugDevice
     * @return Response
     * @throws \Exception
     */
    #[Route(path: '/device/images/delete/{id}/{slugDevice}', name: 'device_image_delete', options: ["expose" => true])]
    public function getPictureDevice(Request $request, int $id, string $slugDevice): Response
    {
        if ($request->headers->get('HX-Request')) {

            $device = $this->deviceRepository->findOneBy(['slug' => $slugDevice]);

            try {

                $this->denyAccessUnlessGranted('edit', $device);

                $picture = $this->devicePictureRepository->find($id);

                $this->denyAccessUnlessGranted('edit', $picture->getDevice());

                $this->devicePictureFactory->delete($picture);

                $this->loggerService->write(Constances::LEVEL_INFO, "Suppression de l'image d'id $id", null, $this->getUser());

            } catch (AccessDeniedHttpException $e){
                $this->loggerService->write(Constances::LEVEL_ERROR, $e->getMessage(), Response::HTTP_UNAUTHORIZED, $this->getUser());
                throw new AccessDeniedHttpException('page 404', null, Response::HTTP_NOT_FOUND);
            }catch (\Exception $e) {
                $this->loggerService->write(Constances::LEVEL_ERROR, $e->getMessage(), null, $this->getUser());
                throw new \Exception('Page 404',Response::HTTP_NOT_FOUND);
            }

            return $this->render('_partial/device/picture_device.html.twig', [
                'data' => $this->devicePictureRepository->findByParent($device),
                'device' => $device,
            ]);
        }

        return throw new \Exception('Page 404',Response::HTTP_NOT_FOUND);
    }



}

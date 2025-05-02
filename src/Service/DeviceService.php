<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Device;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviceService
{

    public function __construct(
        private readonly LoggerService  $loggerService,
        private readonly SessionService $sessionService
    )
    {

    }

    public function isUserValid(Device $device, UserInterface $user): bool{
        return $device->getUser() !== $user;
    }


    /**
     * Comparer deux devices
     * @param Device $device
     * @param Device $newDevice
     * @param array $categories
     * @return bool
     */
    public function isEquivalentTo(Device $device, Device $newDevice, array $categories): bool
    {
        return
            $device->getTitle() === $newDevice->getTitle() &&
            $device->getBody() === $newDevice->getBody() &&
            $device->getPrice() === $newDevice->getPrice() &&
            $device->getStatus() === $newDevice->getStatus() &&
            $device->getLocation() === $newDevice->getLocation() &&
            $device->getPhoneNumber() === $newDevice->getPhoneNumber() &&
            $device->getQuantity() === $newDevice->getQuantity() &&
            $device->isShowPhone() === $newDevice->isShowPhone() &&
            $device->getType() === $newDevice->getType() &&
            $device->getParent() === $newDevice->getParent() &&
            $this->compareCategories($device, $categories);
    }

    /**
     * Comparer categories de deux devices
     * @param Device $device
     * @param array $categories
     * @return bool
     */
    private function compareCategories(Device $device, array $categories): bool
    {
        $idsA = array_map(fn(Category $cat) => $cat->getId(), $device->getCategories()->toArray());
        $idsB = array_map(fn($id) => (int) $id, $categories);

        sort($idsA);
        sort($idsB);

        return $idsA === $idsB;
    }

    public function handleFormStatus(Device $device, $form): string
    {
        if($form->get('draft')->isClicked()) return Constances::DRAFT;
        if($form->has('delete') && $form->get('delete')->isClicked()) return Constances::DELETED;
        return Constances::PENDING;
    }

    /**
     * @param $form
     * @param array $params
     * @param User|null $user
     * @return RedirectResponse
     * @throws \Exception
     */
    public function redirect($form, array $params = [], ?User $user = null): RedirectResponse{
        if($form->get('draft')->isClicked()){
            $message = 'Votre annonce a été sauvergardée';
            $this->loggerService->write(Constances::LEVEL_INFO, $message, Response::HTTP_CREATED, $user);
            return $this->sessionService->redirectWithFlash('app_user_devices', 'success', $message);
        }

        if($form->has('delete') && $form->get('delete')->isClicked()) {
            $message = 'Votre annonce a été supprimée';
            $this->loggerService->write(Constances::LEVEL_INFO, $message, Response::HTTP_CREATED, $user);
            return $this->sessionService->redirectWithFlash('app_user_devices', 'success', $message);
        }

        if($form->get('next')->isClicked()) {
            $message = 'Votre annonce a été sauvergardée';
            $this->loggerService->write(Constances::LEVEL_INFO, $message, Response::HTTP_CREATED, $user);
            return $this->sessionService->redirectWithFlash('app_user_upload_image_device', 'success', $message, $params);
        }

        return $this->sessionService->redirectWithFlash('app_user_devices', 'success', 'Votre annonce a été prise en compte et sera validée par notre équipe');
    }

}

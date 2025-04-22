<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Reservation;
use App\Entity\Device;
use App\Entity\ReservationStatus;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Expr\Array_;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviceService
{

    public function __construct(private readonly LoggerInterface $logger)
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

}

<?php

namespace App\Factory;

use App\Entity\Device;
use App\Repository\CategoryRepository;
use App\Repository\DeviceRepository;
use Psr\Log\LoggerInterface;

readonly class DeviceFactory
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private DeviceRepository   $deviceRepository,
        private LoggerInterface    $logger
    ) {}

    /**
     * @param Device $device
     * @param Device $parent
     * @param array $categories
     * @param bool $isDiff
     * @return Device|null
     */
    public function createWithCategory(Device $device, Device $parent, array $categories = [], bool $isDiff = false): ?Device
    {
        // On enregistre que s'il y a difference
        if($isDiff) return null;

        foreach ($this->convertIdToCategoryEntity($categories) as $category) {
            $device->addCategory($category);
        }

        $device->setParent($parent);
        $device->setCreated(new \DateTime());

        $this->deviceRepository->save($device);

        $this->logger->info(sprintf('Created device "%s" - class: %s', $device->getId(), __CLASS__));

        return $device;
    }

    public function updateByDevice(Device $device): Device
    {
        $device->setUpdated(new \DateTime());
        if($device->getParent() == null) $device->setParent($device);

        $this->deviceRepository->save($device);

        $this->logger->info(sprintf('Updated device "%s" - class: %s', $device->getId(), __CLASS__));
        return $device;
    }

    /**
     * @param array|null $categoriesId
     * @return array
     */
    private function convertIdToCategoryEntity(?array $categoriesId = null): array
    {
        if ($categoriesId == null) return [];

        return array_map(fn ($idCat) => $this->categoryRepository->find((int)$idCat), $categoriesId);
    }

    /**
     * Creer un device à partir d'un autre device
     * @param Device $device
     * @return Device
     */
    public function initDeviceByDeviceId(Device $device): Device {

        $newDevice = new Device();

        $newDevice->setBody($device->getBody());
        $newDevice->setParent($device->getParent());
        $newDevice->setType($device->getType());
        $newDevice->setPrice($device->getPrice());
        $newDevice->setUser($device->getUser());
        $newDevice->setStatus($device->getStatus());
        $newDevice->setTitle($device->getTitle());
        $newDevice->setDeleted($device->getDeleted());
        $newDevice->setPhoneNumber($device->getPhoneNumber());
        $newDevice->setShowPhone($device->getShowPhone());
        $newDevice->setQuantity($device->getQuantity());
        $newDevice->setLocation($device->getLocation());
        $newDevice->setLocation($device->getLocation());
        foreach ($device->getDevicePictures() as $picture){
            $newDevice->addDevicePicture($picture);
        }
        return $newDevice;
    }

}
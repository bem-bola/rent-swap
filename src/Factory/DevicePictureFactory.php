<?php

namespace App\Factory;

use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Repository\CategoryRepository;
use App\Repository\DevicePictureRepository;
use App\Repository\DeviceRepository;
use App\Service\Constances;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

readonly class DevicePictureFactory
{
    public function __construct(
       private readonly EntityManagerInterface $entityManager,
    ) {}

    public function create(Device $device, string $filename): DevicePicture{
        $picture = new DevicePicture();
        $picture->setFilename($filename);
        $picture->setDevice($device);
        $picture->setCreated(new \DateTime());
        $picture->setAlt("Image de l'appareil " .  $device->getTitle());
        $picture->setAlt($device->getTitle());

        $this->entityManager->persist($picture);
        $this->entityManager->flush();

        return $picture;
    }

    /**
     * @param Device $device
     * @return void
     */
    public function dateByDevice(Device $device): void{

        foreach ($device->getDevicePictures() as $picture){
            $picture->setDeleted(new \DateTime());
            $this->entityManager->persist($picture);
        }
        $this->entityManager->flush();
    }

    /**
     * @param DevicePicture $picture
     * @return void
     */
    public function delete(DevicePicture $picture): void{

        $picture->setDeleted(new \DateTime());
        $this->entityManager->persist($picture);

        $this->entityManager->flush();
    }

}
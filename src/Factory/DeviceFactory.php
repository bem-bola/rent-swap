<?php

namespace App\Factory;

use App\Entity\Device;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\DeviceRepository;

class DeviceFactory
{
    public function __construct(private readonly DeviceRepository $deviceRepository) {}
//
//    public function create(User $sender, User $recipient): Conversation
//    {
//        $conversation = new Device();
//
//        $conversation->addUser($sender);
//        $conversation->addUser($recipient);
//        $this->conversationRepository->save($conversation);
//
//        return $conversation;
//    }

    public function initDeviceByDeviceId(Device $device): Device {

        $newDevice = new Device();

        $newDevice->setBody($device->getBody());
        $newDevice->setParent($device->getParent());
        $newDevice->setType($device->getType());
        $newDevice->setPrice($device->getPrice());
        $newDevice->setUser($device->getUser());
        $newDevice->setStatus($device->getStatus());
        $newDevice->setCreated($device->getCreated());
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
        foreach ($device->getCategories() as $category){
            $newDevice->addCategory($category);
        }
        return $newDevice;
    }

}
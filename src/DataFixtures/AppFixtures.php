<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Entity\TypeDevice;
use App\Entity\TypeUser;
use App\Entity\User;
use App\Service\HttpClientService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Random\RandomException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AppFixtures extends Fixture
{
    private string $dataFixture;
    private string $uploadFileDevice;

    private UserPasswordHasherInterface $passwordHasher;

    private HttpClientService $httpClientService;

    /**
     * @param string $dataFixture
     * @param string $uploadFileDevice
     * @param UserPasswordHasherInterface $passwordHasher
     * @param HttpClientService $httpClientService
     */
    public function __construct(
        string $dataFixture,
        string $uploadFileDevice,
        UserPasswordHasherInterface $passwordHasher,
        HttpClientService $httpClientService
    )
    {
        $this->dataFixture = $dataFixture;
        $this->uploadFileDevice = $uploadFileDevice;
        $this->passwordHasher = $passwordHasher;
        $this->httpClientService = $httpClientService;
    }

    private function getDataFile(string $filename): array{
        return json_decode(file_get_contents($this->dataFixture."$filename.json"));
    }

    private function pushCatergories(ObjectManager $manager): array {

        $categories = [
            'Ordinateurs portables',
            'Ordinateurs de bureau',
            'Moniteurs',
            'Claviers et souris',
            'Disques durs externes',
            'Accessoires informatiques',
            'Appareils informatiques',
            'Téléphones et tablettes',
            'Appareils photo et caméras',
            'Équipements audio et vidéo',
            'Smartphones',
            'Tablettes',
            "Accessoires mobiles",
            'Appareils photo numériques',
            'Caméra',
            "Accessoires mobiles",
            'Trépieds et supports',
            'Casques et écouteurs',
            'Enceintes Bluetooth',
            "Microphones",
            'Projecteurs',
            'Systèmes de son surround',
            'Home cinéma'
        ];

        $results = [];

        foreach ($categories as $key => $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);

            $this->addReference("tag_$key", $category);

            $results[] = $category;
        }

        $manager->flush();
        $manager->flush();

        return $results;
    }

    private function pushTypeDevice(ObjectManager $manager): array{
        $types = ['rental', 'swap', 'free'];

        $results =[];
        foreach ($types as $type) {
            $typeDevice = new TypeDevice();
            $typeDevice->setName($type);
            $manager->persist($typeDevice);

            $results[] = $typeDevice;
        }

        $manager->flush();

        return $results;
    }

    private function pushTypeUser(ObjectManager $manager): array{
        $types = ['professional', 'particular', 'organization'];

        $results = [];
        foreach ($types as $type) {
            $typeUser = new TypeUser();
            $typeUser->setName($type);
            $manager->persist($typeUser);

            $results[] = $typeUser;
        }

        $manager->flush();

        return $results;
    }

    /**
     * @throws Exception
     */
    private function pushUser(ObjectManager $manager, array $typesUser): array{

        $usersData =  $this->getDataFile('user');

        $results = [];
        foreach ($usersData as $userData) {
            $user = new User();
            $user->setUsername($userData->username);
            $user->setEmail($userData->email);
            $user->setFirstname($userData->firstname);
            $user->setLastname($userData->lastname);
            $user->setCreated(new \DateTime($userData->created));
            $user->setIsVerified($userData->isVerified);
            $user->setIsDeleted($userData->isDeleted);
            $user->setType($typesUser[$userData->type]);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData->password);
            $user->setPassword($hashedPassword);

            $manager->persist($user);

            $results[] = $user;
        }

        $manager->flush();

        return $results;
    }

    /**
     * @throws RandomException
     */
    private function pushDevice(ObjectManager $manager, array $users, $typesDevice): array{

        $devicesData =  $this->getDataFile('device');

        $results = [];
        foreach ($devicesData as $deviceData) {
            $device = new Device();
            $device->setUser($users[rand(0,22)]);
            $device->setBody($deviceData->body);
            $device->setPrice($deviceData->price);
            $device->setTitle($deviceData->title);
            $device->setStatus('pending');
            $device->setShowPhone($deviceData->showPhone);
            $device->setCreated(new \DateTime($deviceData->created));
            $device->setLocation($deviceData->location);
            $device->setPhoneNumber($deviceData->phoneNumber);
            $device->setQuantity($deviceData->quantity);
            $device->setType($typesDevice[rand(0,2)]);
            $device->setSlug(uniqid().time().uniqid().bin2hex(random_bytes(50)).bin2hex($deviceData->price));

            $device->addCategory($this->getReference('tag_'.rand(0,10), Category::class));
            $device->addCategory($this->getReference('tag_'.rand(11,15), Category::class));
            $device->addCategory($this->getReference('tag_'.rand(16,22), Category::class));

            $results[] = $device;
        }

        $manager->flush();

        return $results;
    }


    /**
     * @throws Exception
     */
    private function pushPictureUser(ObjectManager $manager, array $devices): void{

        foreach (scandir($this->uploadFileDevice) as $key => $picture) {
            if($key < 2) continue;
            $devicePicture = new DevicePicture();
            $devicePicture->setCreated(new \DateTime());
            $devicePicture->setDevice($devices[rand(0,99)]);
            $devicePicture->setFilename($picture);

            $manager->persist($devicePicture);
        }

        $manager->flush();

    }
    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {

        $this->pushCatergories($manager);

        $typesDevice = $this->pushTypeDevice($manager);

        $typesUser = $this->pushTypeUser($manager);

        $users = $this->pushUser($manager, $typesUser);

        $device = $this->pushDevice($manager, $users, $typesDevice);

        $this->pushPictureUser($manager, $device);

    }
}

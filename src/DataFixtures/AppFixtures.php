<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Device;
use App\Entity\DevicePicture;
use App\Entity\SubCategory;
use App\Entity\TypeDevice;
use App\Entity\TypeUser;
use App\Entity\User;
use App\Service\HttpClientService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
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
     * @param UserPasswordHasherInterface $passwordHasher
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

        $categories = ['Appareils informatiques', 'Téléphones et tablettes', 'Appareils photo et caméras', 'Équipements audio et vidéo'];
        $subCategories = [
            ['Ordinateurs portables', 'Ordinateurs de bureau', 'Moniteurs', 'Claviers et souris', 'Disques durs externes', 'Accessoires informatiques'],
            ['Smartphones', 'Tablettes', "Accessoires mobiles"],
            ['Appareils photo numériques', 'Caméra', "Accessoires mobiles", 'Trépieds et supports'],
            ['Casques et écouteurs', 'Enceintes Bluetooth', "Microphones", 'Projecteurs', 'Systèmes de son surround', 'Home cinéma'],
        ];

        $results = [];
        $tag = 0;
        foreach ($categories as $key => $categoryData) {
            $category = new Category();
            $category->setName($categoryData);
            $manager->persist($category);

            foreach ($subCategories[$key] as $subCategoryData){
                $subCategory = new SubCategory();
                $subCategory->setName($subCategoryData);
                $subCategory->setCategory($category);
                $manager->persist($subCategory);

                $this->addReference("tag_$tag", $subCategory);
                $tag++;
                $results[] = $subCategory;
            }

        }

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
     * @throws \Exception
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
    private function pushDevice(ObjectManager $manager, array $users, $typesDevice, $subCategories): array{

        $devicesData =  $this->getDataFile('device');

        $results = [];
        foreach ($devicesData as $deviceData) {
            $device = new Device();
            $device->setUser($users[0]);
            $device->setBody($deviceData->body);
            $device->setPrice($deviceData->price);
            $device->setTitle($deviceData->title);
            $device->setStatus('pending');
            $device->setShowPhone($deviceData->showPhone);
            $device->setCreated(new \DateTime($deviceData->created));
            $device->setLocation($deviceData->location);
            $device->setPhoneNumber($deviceData->phoneNumber);
            $device->setQuantity($deviceData->quantity);
            $device->setType($typesDevice[$deviceData->type]);
            $device->setSlug(uniqid().time().uniqid().bin2hex(random_bytes(50)).bin2hex($deviceData->price));

            $device->addSubCategory($this->getReference('tag_'.rand(0,9), SubCategory::class));
            $device->addSubCategory($this->getReference('tag_'.rand(10,18), SubCategory::class));

            $results[] = $device;
        }

        $manager->flush();

        return $results;
    }


    /**
     * @throws \Exception
     */
    private function pushPictureUser(ObjectManager $manager, array $devices): array{

        $devicesPicture =  $this->getDataFile('devicePicture');

        $results = [];
        foreach ($devicesPicture as $picture) {
            $devicePicture = new DevicePicture();
            $devicePicture->setCreated(new \DateTime($picture->created));
            $devicePicture->setDevice($devices[$picture->device]);

            try {
                $filename = uniqid().time().bin2hex(random_bytes(50)).bin2hex($devices[$picture->device]->getTitle()) . '.jpg';
                $targetPath = $this->uploadFileDevice . $filename;

                $this->httpClientService->downloadAndSaveImageUnsPlash($targetPath);

                $devicePicture->setFilename($filename);

            } catch (\Exception|DecodingExceptionInterface|TransportExceptionInterface $e) {
                $devicePicture->setFilename('https://picsum.photos/700/300');
            }

            $manager->persist($devicePicture);
        }

        $manager->flush();

        return $results;
    }
    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        $subCategories = $this->pushCatergories($manager);

        $typesDevice = $this->pushTypeDevice($manager);

        $typesUser = $this->pushTypeUser($manager);

        $users = $this->pushUser($manager, $typesUser);

        $device = $this->pushDevice($manager, $users, $typesDevice, $subCategories);

        $this->pushPictureUser($manager, $device);

    }
}

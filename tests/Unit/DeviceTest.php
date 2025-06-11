<?php

namespace App\Tests\Unit;

use App\Entity\Category;
use App\Entity\Device;
use App\Entity\TypeDevice;
use App\Entity\User;
use App\Service\Constances;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeviceTest extends KernelTestCase
{
    /**
     * Retourne le validateur Symfony
     */
    private function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        return static::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Génère une entité Device avec des données valides
     * Possibilité de surcharger n’importe quel attribut via $overrides
     */
    private function getEntity(array $overrides = []): Device
    {
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);

        // Requêtes vers les entités liées
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => 'test']);
        $typeDevice = $entityManager->getRepository(TypeDevice::class)->findOneBy(['name' => 'rental']);
        $parent = $entityManager->getRepository(Device::class)->findOneBy(['title' => 'test']);
        $categories = $entityManager->getRepository(Category::class)->findBy([], [], 3);

        // Création de l’objet Device
        $device = new Device();
        $device->setBody("Body #1")
            ->setPrice(15.25)
            ->setShowPhone(true)
            ->setCreated(new \DateTimeImmutable())
            ->setDeleted(null)
            ->setTitle("Title #1")
            ->setStatus(Constances::PENDING)
            ->setLocation('Paris')
            ->setPhoneNumber('0125365845')
            ->setQuantity(10)
            ->setUpdated(null)
            ->setValidated(null)
            ->setUser($user)
            ->setParent($parent)
            ->setType($typeDevice);

        foreach ($categories as $category) {
            $device->addCategory($category);
        }

        // Surcharge d’attributs si précisé
        foreach ($overrides as $property => $value) {
            $setter = 'set' . ucfirst($property);
            if (method_exists($device, $setter)) {
                $device->$setter($value);
            }
        }

        return $device;
    }

    /**
     * Test de base : entité entièrement valide
     */
    public function testValidDevice(): void
    {
        $device = $this->getEntity();
        $errors = $this->getValidator()->validate($device);
        $this->assertCount(0, $errors, 'L’entité Device valide ne devrait pas produire d’erreurs');
    }

    /**
     * Test : titre trop court
     */
    public function testInvalidWithShortTitle(): void
    {
        $device = $this->getEntity(['title' => 'A']);
        $errors = $this->getValidator()->validate($device);
        $this->assertGreaterThan(0, count($errors), 'Titre trop court doit être invalide');
    }

    /**
     * @return void
     */
    public function testInvalidWithBlankTitle(): void
    {
        $device = $this->getEntity(['title' => '']);
        $errors = $this->getValidator()->validate($device);
        $this->assertGreaterThan(0, count($errors), 'Titre ne peut pas être nul');
    }

    /**
     * Test : body obligatoire
     */
    public function testInvalidWithoutBody(): void
    {
        $device = $this->getEntity(['body' => '']);
        $errors = $this->getValidator()->validate($device);
        $this->assertGreaterThan(0, count($errors), 'Body vide doit être invalide');
    }

    /**
     * Test : location obligatoire
     */
    public function testInvalidWithoutLocation(): void
    {
        $device = $this->getEntity(['location' => '']);
        $errors = $this->getValidator()->validate($device);
        $this->assertGreaterThan(0, count($errors), 'Location vide doit être invalide');
    }

}

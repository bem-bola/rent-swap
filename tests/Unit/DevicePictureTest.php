<?php

namespace App\Tests\Unit;

use App\Entity\Device;
use App\Entity\DevicePicture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DevicePictureTest extends KernelTestCase
{
    private function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        return static::getContainer()->get(ValidatorInterface::class);
    }

    private function getEntity(array $overrides = []): DevicePicture
    {
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        $device = $em->getRepository(Device::class)->findOneBy(['title' => 'test']);

        $picture = new DevicePicture();
        $picture->setDevice($device);
        $picture->setFilename('image.jpg');
        $picture->setCreated(new \DateTimeImmutable());
        $picture->setDeleted(null);
        $picture->setAlt('Image description');
        $picture->setTitle('Titre image');

        foreach ($overrides as $property => $value) {
            $setter = 'set' . ucfirst($property);
            if (method_exists($picture, $setter)) {
                $picture->$setter($value);
            }
        }

        return $picture;
    }

    public function testValidDevicePicture(): void
    {
        $picture = $this->getEntity();
        $errors = $this->getValidator()->validate($picture);
        $this->assertCount(0, $errors, "Une image valide ne devrait générer aucune erreur.");
    }

    public function testInvalidFilename(): void
    {
        echo "\nDébut test : filename vide\n";

        $picture = $this->getEntity(['filename' => '']);
        $errors = $this->getValidator()->validate($picture);

        $this->assertGreaterThan(
            0,
            count($errors),
            'L’image sans nom de fichier doit être invalide'
        );

        echo "Fin test : filename vide\n";
    }
}

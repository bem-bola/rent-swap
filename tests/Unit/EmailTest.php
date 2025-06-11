<?php

namespace App\Tests\Unit;

use App\Entity\Email;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class EmailTest extends KernelTestCase
{
    private function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        return static::getContainer()->get(ValidatorInterface::class);
    }

    private function getEntity(array $overrides = []): Email
    {
        $container = static::getContainer();
        $em = $container->get(EntityManagerInterface::class);

        // Récupère deux utilisateurs pour les liaisons
        $sender = $em->getRepository(User::class)->findOneBy(['username' => 'test']);
        $receiver = $em->getRepository(User::class)->findOneBy(['username' => 'test2']);

        $email = new Email();
        $email->setSender($sender);
        $email->setReceiver($receiver);
        $email->setContent("Contenu du message");
        $email->setAdressSender("test@example.com");
        $email->setObject("Objet du message");
        $email->setCreated(new \DateTime());

        foreach ($overrides as $property => $value) {
            $setter = 'set' . ucfirst($property);
            if (method_exists($email, $setter)) {
                $email->$setter($value);
            }
        }

        return $email;
    }

    public function testValidEmail(): void
    {
        $email = $this->getEntity();
        $errors = $this->getValidator()->validate($email);
        $this->assertCount(0, $errors, "L'email valide ne devrait générer aucune erreur.");
    }

    private const INVALID_VALUES = [
        [
            'property' => ['sender' => null],
            'message' => 'Un email sans sender doit être invalide',
            'description' => 'absence de sender'
        ],
        [
            'property' => ['receiver' => null],
            'message' => 'Un email sans receiver doit être invalide',
            'description' => 'absence de receiver'
        ],
        [
            'property' => ['content' => ''],
            'message' => 'Un email avec un contenu vide doit être invalide',
            'description' => 'contenu vide'
        ],
    ];

    public function testInvalidEmailFields(): void
    {
        foreach (self::INVALID_VALUES as $index => $case) {
            echo "\nDébut test : {$case['description']}\n";

            $email = $this->getEntity($case['property']);
            $errors = $this->getValidator()->validate($email);

            $this->assertGreaterThan(
                0,
                count($errors),
                $case['message']
            );

            echo "Fin test : {$case['description']}\n";
        }
    }
}

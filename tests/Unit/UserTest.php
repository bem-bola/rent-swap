<?php

namespace App\Tests\Unit;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTest extends KernelTestCase
{
    private const INVALID_VALUES = [
        [
            'property' => ['lastname' => 'A8'],
            'message' => 'Un lastname avec un caractère non alphabétique doit générer une erreur',
            'description' => 'longueur ou format de lastname'
        ],
        [
            'property' => ['firstname' => 'A8'],
            'message' => 'Un firstname avec un caractère non alphabétique doit générer une erreur',
            'description' => 'longueur ou format de firstname'
        ],
        [
            'property' => ['username' => 'A8'],
            'message' => 'Un username avec un caractère non alphabétique doit générer une erreur',
            'description' => 'longueur ou format de username'
        ],

        [
            'property' => ['lastname' => ''],
            'message' => 'Un lastname vide doit générer une erreur',
            'description' => 'absence de lastname'
        ],
        [
            'property' => ['firstname' => ''],
            'message' => 'Un firstname vide doit générer une erreur',
            'description' => 'absence de firstname'
        ],
        [
            'property' => ['username' => ''],
            'message' => 'Un username vide doit générer une erreur',
            'description' => 'absence de username'
        ],

        [
            'property' => ['lastname' => 'A'],
            'message' => 'Un lastname avec moins de 2 caractères doit générer une erreur',
            'description' => 'lastname trop court'
        ],
        [
            'property' => ['firstname' => 'A'],
            'message' => 'Un firstname avec moins de 2 caractères doit générer une erreur',
            'description' => 'firstname trop court'
        ],
        [
            'property' => ['username' => 'A'],
            'message' => 'Un username avec moins de 2 caractères doit générer une erreur',
            'description' => 'username trop court'
        ],

        [
            'property' => ['email' => ''],
            'message' => 'Un email vide doit générer une erreur',
            'description' => 'email vide'
        ],
        [
            'property' => ['email' => 'invalid-email'],
            'message' => 'Un email invalide doit générer une erreur',
            'description' => 'format invalide de email'
        ],

        [
            'property' => ['password' => 'password'],
            'message' => 'Un mot de passe doit contenir au moins 12 caractères, avec au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
            'description' => 'mot de passe faible'
        ],
        [
            'property' => ['password' => ''],
            'message' => 'Un mot de passe vide doit générer une erreur',
            'description' => 'mot de passe vide'
        ],
    ];


    private function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        return static::getContainer()->get(ValidatorInterface::class);
    }

    private function getEntity(array $overrides = []): User
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setFirstname('Firstname');
        $user->setLastname('Lastname');
        $user->setPassword('Password123!');
        $user->setRoles(['ROLE_USER']);
        $user->setCreated(new \DateTimeImmutable());
        $user->setUsername('TestUser');
        $user->setIsVerified(true);

        foreach ($overrides as $property => $value) {
            $setter = 'set' . ucfirst($property);
            if (method_exists($user, $setter)) {
                $user->$setter($value);
            }
        }

        return $user;
    }

    public function testValidUser(): void
    {
        echo "\nDébut test utilisateur\n\n";
        $user = $this->getEntity();
        $errors = $this->getValidator()->validate($user);
        $this->assertCount(0, $errors, 'L’utilisateur complet devrait être valide');
    }

    /**
     * Test : rôle vide autorisé ? (à adapter selon logique métier)
     */
    public function testEmptyRolesIsValid(): void
    {
        echo "\nDébut test rôle utilisateur null";
        $user = $this->getEntity(['roles' => []]);
        $errors = $this->getValidator()->validate($user);
        $this->assertCount(0, $errors, 'Les rôles vides sont tolérés car ROLE_USER est forcé dans getRoles()');
        echo "\nFin test rôle utilisateur null\n\n";
    }


    public function testInvalidUserFields(): void
    {
        foreach (self::INVALID_VALUES as $case) {
            echo "\nDébut test ". $case['description'];
            $user = $this->getEntity($case['property']);
            $errors = $this->getValidator()->validate($user);
            $this->assertGreaterThan(0, count($errors), $case['message']);
            echo "\nFin test ". $case['description'] . "\n\n";
        }
    }

}

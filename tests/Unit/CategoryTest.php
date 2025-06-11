<?php

namespace App\Tests\Unit;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryTest extends KernelTestCase
{
    /**
     * Récupère le service Validator de Symfony
     */
    private function getValidator(): ValidatorInterface
    {
        self::bootKernel();
        return static::getContainer()->get(ValidatorInterface::class);
    }

    /**
     * Crée une catégorie valide
     */
    private function getEntity(array $overrides = []): Category
    {
        $category = new Category();
        $category->setName('Informatique');

        foreach ($overrides as $property => $value) {
            $setter = 'set' . ucfirst($property);
            if (method_exists($category, $setter)) {
                $category->$setter($value);
            }
        }

        return $category;
    }

    /**
     * Test : catégorie valide
     */
    public function testValidCategory(): void
    {
        $category = $this->getEntity();
        $errors = $this->getValidator()->validate($category);
        $this->assertCount(0, $errors, 'Une catégorie valide ne doit pas générer d\'erreurs.');
    }

    /**
     * Cas invalides à tester
     */
    private const INVALID_VALUES = [
        [
            'property' => ['name' => ''],
            'message' => 'Le nom vide doit générer une erreur',
            'description' => 'Nom vide'
        ],
        [
            'property' => ['name' => 'A'],
            'message' => 'Un nom trop court doit générer une erreur',
            'description' => 'Nom trop court'
        ]
    ];

    /**
     * Test : cas invalides
     */
    public function testInvalidCategoryFields(): void
    {
        foreach (self::INVALID_VALUES as $index => $case) {
            echo "\nDébut test : {$case['description']}\n";

            $category = $this->getEntity($case['property']);
            $errors = $this->getValidator()->validate($category);

            $this->assertGreaterThan(
                0,
                count($errors),
                $case['message'],
            );

            echo "Fin test : {$case['description']}\n";
        }
    }
}

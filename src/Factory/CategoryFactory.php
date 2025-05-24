<?php

namespace App\Factory;

use App\Entity\Category;
use App\Entity\User;
use App\Service\LoggerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class CategoryFactory
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerService          $loggerService
    ) {}

    public function update(Category $category, User $user): Category
    {
       $this->entityManager->persist($category);
       $this->entityManager->flush();

       $this->loggerService->write('info', 'Message updated ' . $category->getId(), Response::HTTP_CREATED, $user);
        return $category;
    }

}
<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/category', name: 'app_category_')]
final class CategoryController extends AbstractController
{
    public function __construct(private readonly CategoryRepository $categoryRepository)
    {}
    #[Route('/search', name: 'search', options: ["expose" => true])]
    public function index(Request $request): Response
    {
        $q = $request->query->get('q');
        $categories = $this->categoryRepository->findByLikeByName($q);

        return $this->json($categories, Response::HTTP_OK);
    }
}

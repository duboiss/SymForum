<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class CategoryPanelController extends BaseController
{
    /**
     * @Route("/categories", name="panel.categories")
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAllCategories();

        return $this->render('panel/categories.html.twig', [
            'categories' => $categories
        ]);
    }
}
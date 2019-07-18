<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/forums", name="forums")
     * @param CategoryRepository $repo
     * @return Response
     */
    public function index(CategoryRepository $repo)
    {
        $categories = $repo->findBy([], ['position' => 'ASC']);

        return $this->render('forums/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/forums/{slug}", name="forums.category")
     * @param Category $category
     * @return Response
     */
    public function category(Category $category)
    {
        return $this->render('forums/category.html.twig', [
            'category' => $category,
        ]);
    }
}

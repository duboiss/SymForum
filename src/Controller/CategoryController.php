<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ForumRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends BaseController
{
    /**
     * @Route("/forums/c/{slug}", name="category.show", requirements={"slug"="^(?:[^\d])[\w\-_]+?$"})
     * @param Category $category
     * @param ForumRepository $forumRepository
     * @return Response
     */
    public function category(Category $category, ForumRepository $forumRepository): Response
    {
        $forums = $forumRepository->findForumsByCategory($category);

        return $this->render('forums/category.html.twig', [
            'category' => $category,
            'forums' => $forums,
        ]);
    }
}

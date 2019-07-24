<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ForumRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/forums", name="forums.index")
     * @param CategoryRepository $repo
     * @param UserRepository $usersRepo
     * @param ForumRepository $forumsRepo
     * @return Response
     */
    public function index(CategoryRepository $repo, UserRepository $usersRepo, ForumRepository $forumsRepo)
    {
        $categories = $repo->findBy([], ['position' => 'ASC']);

        $lastRegistered = $usersRepo->findOneBy([], ['registrationDate' => 'DESC']);
        $nbUsers = $usersRepo->count([]);
        $nbThreads = $forumsRepo->count([]);

        return $this->render('forums/index.html.twig', [
            'categories' => $categories,
            'nbUsers' => $nbUsers,
            'lastRegistered' => $lastRegistered,
            'nbThreads' => $nbThreads
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

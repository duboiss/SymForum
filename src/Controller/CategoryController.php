<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ForumRepository;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/forums", name="forums.index")
     * @param CategoryRepository $categoriesRepo
     * @param UserRepository $usersRepo
     * @param MessageRepository $messagesRepo
     * @param ThreadRepository $threadsRepo
     * @return Response
     * @throws \Exception
     */
    public function index(CategoryRepository $categoriesRepo, UserRepository $usersRepo, MessageRepository $messagesRepo, ThreadRepository $threadsRepo)
    {
        $categories = $categoriesRepo->findBy([], ['position' => 'ASC']);

        $onlineUsers = $usersRepo->findOnlineUsers();
        $lastRegistered = $usersRepo->findOneBy([], ['registrationDate' => 'DESC']);
        $nbUsers = $usersRepo->count([]);
        $nbMessages = $messagesRepo->count([]);
        $nbThreads = $threadsRepo->count([]);

        return $this->render('forums/index.html.twig', [
            'categories' => $categories,
            'onlineUsers' => $onlineUsers,
            'nbUsers' => $nbUsers,
            'lastRegistered' => $lastRegistered,
            'nbMessages' => $nbMessages,
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

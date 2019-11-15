<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use App\Service\OptionService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends BaseController
{
    /**
     * @Route("/forums", name="forums.index")
     * @param CategoryRepository $categoriesRepo
     * @param UserRepository $usersRepo
     * @param MessageRepository $messagesRepo
     * @param ThreadRepository $threadsRepo
     * @param OptionService $optionService
     * @return Response
     * @throws \Exception
     */
    public function index(CategoryRepository $categoriesRepo, UserRepository $usersRepo, MessageRepository $messagesRepo, ThreadRepository $threadsRepo, OptionService $optionService): Response
    {
        $categories = $categoriesRepo->findAllCategories();

        $onlineUsers = $usersRepo->findOnlineUsers();
        $maxOnlineUsers = $optionService->get("max_online_users", "0");
        $maxOnlineUsersDate = $optionService->get("max_online_users_date");
        $nbUsers = $usersRepo->count([]);
        $lastRegistered = $usersRepo->findLastRegistered();
        $nbMessages = $messagesRepo->count([]);
        $nbThreads = $threadsRepo->count([]);

        return $this->render('forums/index.html.twig', [
            'categories' => $categories,
            'onlineUsers' => $onlineUsers,
            'maxOnlineUsers' => $maxOnlineUsers,
            'maxOnlineUsersDate' => $maxOnlineUsersDate,
            'nbUsers' => $nbUsers,
            'lastRegistered' => $lastRegistered,
            'nbMessages' => $nbMessages,
            'nbThreads' => $nbThreads
        ]);
    }

    /**
     * @Route("/forums/c/{slug}", name="category.show", requirements={"slug"="^(?:[^\d])[\w\-_]+?$"})
     * @param Category $category
     * @return Response
     */
    public function category(Category $category): Response
    {
        return $this->render('category/category.html.twig', [
            'category' => $category,
        ]);
    }
}

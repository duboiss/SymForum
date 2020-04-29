<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Forum;
use App\Repository\CategoryRepository;
use App\Repository\ForumRepository;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use App\Service\ForumService;
use App\Service\OptionService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends BaseController
{
    /**
     * @Route("/forums", name="forum.index", methods={"GET"})
     * @param CategoryRepository $categoriesRepo
     * @param UserRepository $userRepository
     * @param MessageRepository $messageRepository
     * @param ThreadRepository $threadRepository
     * @param OptionService $optionService
     * @return Response
     * @throws Exception
     */
    public function index(CategoryRepository $categoriesRepo, UserRepository $userRepository, MessageRepository $messageRepository, ThreadRepository $threadRepository, OptionService $optionService): Response
    {
        return $this->render('pages/forums.html.twig', [
            'categories' => $categoriesRepo->findAllCategories(),
            'onlineUsers' => $userRepository->findOnlineUsers(),
            'maxOnlineUsers' => $optionService->get('max_online_users', '0'),
            'maxOnlineUsersDate' => $optionService->get('max_online_users_date'),
            'nbUsers' => $userRepository->count([]),
            'lastRegistered' => $userRepository->findLastRegistered(),
            'nbMessages' => $messageRepository->count([]),
            'nbThreads' => $threadRepository->count([])
        ]);
    }

    /**
     * @Route("/forums/{slug}", name="forum.show", requirements={"id"="\d+", "slug"="[\w\-_]+?$"}, methods={"GET"})
     * @param Forum $forum
     * @param ThreadRepository $threadRepository
     * @return Response
     */
    public function show(Forum $forum, ThreadRepository $threadRepository): Response
    {
        $threads = $threadRepository->findThreadsByForum($forum);

        return $this->render('forum/show.html.twig', [
            'forum' => $forum,
            'threads' => $threads
        ]);
    }

    /**
     * @Route("/forums/c/{slug}", name="category.show", requirements={"slug"="^(?:[^\d])[\w\-_]+?$"}, methods={"GET"})
     * @param Category $category
     * @param ForumRepository $forumRepository
     * @return Response
     */
    public function category(Category $category, ForumRepository $forumRepository): Response
    {
        $forums = $forumRepository->findForumsByCategory($category);

        return $this->render('forum/category.html.twig', [
            'category' => $category,
            'forums' => $forums,
        ]);
    }

    /**
     * @Route("/forums/{id}-{slug}/lock", name="forum.lock", methods={"GET"})
     * @IsGranted("LOCK", subject="forum")
     * @param Forum $forum
     * @param ForumService $forumService
     * @return Response
     */
    public function lock(Forum $forum, ForumService $forumService): Response
    {
        $forumService->lock($forum);
        $this->addCustomFlash('success', 'Forum', 'Le forum a été fermé !');

        return $this->redirectToRoute('forum.show', [
            'slug' => $forum->getSlug()
        ]);
    }

    /**
     * @Route("/forums/{id}-{slug}/unlock", name="forum.unlock", methods={"GET"})
     * @IsGranted("LOCK", subject="forum")
     * @param Forum $forum
     * @param ForumService $forumService
     * @return Response
     */
    public function unlock(Forum $forum, ForumService $forumService): Response
    {
        $forumService->unlock($forum);
        $this->addCustomFlash('success', 'Forum', 'Le forum a été ouvert !');

        return $this->redirectToRoute('forum.show', [
            'slug' => $forum->getSlug()
        ]);
    }
}

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
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/forums')]
class ForumController extends AbstractBaseController
{
    #[Route(path: '/', name: 'forum.index', methods: ['GET'])]
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
            'nbThreads' => $threadRepository->count([]),
        ]);
    }

    #[Route(path: '/{slug}', name: 'forum.show', requirements: ['id' => '\d+', 'slug' => '[\w\-_]+?$'], methods: ['GET'])]
    public function show(Forum $forum, ThreadRepository $threadRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $threadRepository->findThreadsByForumQb($forum),
            $request->query->getInt('page', 1),
            15
        );

        return $this->render('forum/show.html.twig', [
            'forum' => $forum,
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/c/{slug}', name: 'category.show', requirements: ['slug' => '^(?:[^\d])[\w\-_]+?$'], methods: ['GET'])]
    public function category(Category $category, ForumRepository $forumRepository): Response
    {
        return $this->render('forum/category.html.twig', [
            'category' => $category,
            'forums' => $forumRepository->findForumsByCategory($category),
        ]);
    }

    #[IsGranted('LOCK', subject: 'forum')]
    #[Route(path: '/{id}-{slug}/lock', name: 'forum.lock', methods: ['GET'])]
    public function lock(Forum $forum, ForumService $forumService): Response
    {
        $forumService->lock($forum);
        $this->addCustomFlash('success', 'Forum', 'Le forum a été fermé !');

        return $this->redirectToRoute('forum.show', [
            'slug' => $forum->getSlug(),
        ]);
    }

    #[IsGranted('LOCK', subject: 'forum')]
    #[Route(path: '/{id}-{slug}/unlock', name: 'forum.unlock', methods: ['GET'])]
    public function unlock(Forum $forum, ForumService $forumService): Response
    {
        $forumService->unlock($forum);
        $this->addCustomFlash('success', 'Forum', 'Le forum a été ouvert !');

        return $this->redirectToRoute('forum.show', [
            'slug' => $forum->getSlug(),
        ]);
    }
}

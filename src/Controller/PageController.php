<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use App\Service\OptionService;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends BaseController
{
    /**
     * @Route("/forums", name="forums.index")
     * @param CategoryRepository $categoriesRepo
     * @param UserRepository $usersRepo
     * @param MessageRepository $messagesRepo
     * @param ThreadRepository $threadsRepo
     * @param OptionService $optionService
     * @return Response
     * @throws Exception
     */
    public function index(CategoryRepository $categoriesRepo, UserRepository $usersRepo, MessageRepository $messagesRepo, ThreadRepository $threadsRepo, OptionService $optionService): Response
    {
        return $this->render('pages/index.html.twig', [
            'categories' => $categoriesRepo->findAllCategories(),
            'onlineUsers' => $usersRepo->findOnlineUsers(),
            'maxOnlineUsers' => $optionService->get("max_online_users", "0"),
            'maxOnlineUsersDate' => $optionService->get("max_online_users_date"),
            'nbUsers' => $usersRepo->count([]),
            'lastRegistered' => $usersRepo->findLastRegistered(),
            'nbMessages' => $messagesRepo->count([]),
            'nbThreads' => $threadsRepo->count([])
        ]);
    }

    /**
     * @Route("/members", name="page.members")
     * @param UserRepository $repo
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function members(UserRepository $repo, Request $request, PaginatorInterface $paginator): Response
    {
        $membersQb = $repo->findAllMembersQb();

        $pagination = $paginator->paginate(
            $membersQb,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('pages/members.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/team", name="page.team")
     * @param UserRepository $repo
     * @return Response
     */
    public function team(UserRepository $repo): Response
    {
        $administrators = $repo->findByRole('ROLE_ADMIN');
        $moderators = $repo->findByRole('ROLE_MODERATOR');

        return $this->render('pages/team.html.twig', [
            'administrators' => $administrators,
            'moderators' => $moderators
        ]);
    }
}

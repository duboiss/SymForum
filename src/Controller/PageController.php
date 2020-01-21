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
     * @Route("/")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('forums.index');
    }

    /**
     * @Route("/forums", name="forums.index")
     * @param CategoryRepository $categoriesRepo
     * @param UserRepository $userRepository
     * @param MessageRepository $messageRepository
     * @param ThreadRepository $threadRepository
     * @param OptionService $optionService
     * @return Response
     * @throws Exception
     */
    public function forums(CategoryRepository $categoriesRepo, UserRepository $userRepository, MessageRepository $messageRepository, ThreadRepository $threadRepository, OptionService $optionService): Response
    {
        return $this->render('pages/index.html.twig', [
            'categories' => $categoriesRepo->findAllCategories(),
            'onlineUsers' => $userRepository->findOnlineUsers(),
            'maxOnlineUsers' => $optionService->get("max_online_users", "0"),
            'maxOnlineUsersDate' => $optionService->get("max_online_users_date"),
            'nbUsers' => $userRepository->count([]),
            'lastRegistered' => $userRepository->findLastRegistered(),
            'nbMessages' => $messageRepository->count([]),
            'nbThreads' => $threadRepository->count([])
        ]);
    }

    /**
     * @Route("/members", name="page.members")
     * @param UserRepository $userRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function members(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $membersQb = $userRepository->findAllMembersQb();

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
     * @param UserRepository $userRepository
     * @return Response
     */
    public function team(UserRepository $userRepository): Response
    {
        $administrators = $userRepository->findByRole('ROLE_ADMIN');
        $moderators = $userRepository->findByRole('ROLE_MODERATOR');

        return $this->render('pages/team.html.twig', [
            'administrators' => $administrators,
            'moderators' => $moderators
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\UserRepository;
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
        return $this->redirectToRoute('forum.index');
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
        $pagination = $paginator->paginate(
            $userRepository->findAllMembersQb(),
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
        return $this->render('pages/team.html.twig', [
            'administrators' => $userRepository->findByRole('ROLE_ADMIN'),
            'moderators' => $userRepository->findByRole('ROLE_MODERATOR')
        ]);
    }
}

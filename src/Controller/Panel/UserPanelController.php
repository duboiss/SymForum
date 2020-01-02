<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class UserPanelController extends BaseController
{
    /**
     * @Route("/users", name="panel.users")
     * @param UserRepository $userRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $usersQb = $userRepository->findAllMembersQb();

        $pagination = $paginator->paginate(
            $usersQb,
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('panel/users/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/users/{slug}", name="panel.user.details")
     * @param User $user
     * @return Response
     */
    public function details(User $user): Response
    {
        return $this->render('panel/users/user.html.twig', [
            'user' => $user
        ]);
    }
}

<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
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
     * @param UserRepository $repo
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(UserRepository $repo, Request $request, PaginatorInterface $paginator): Response
    {
        $usersQb = $repo->findAllMembersQb();

        $pagination = $paginator->paginate(
            $usersQb,
            $request->query->getInt('page', 1),
            30
        );

        return $this->render('panel/users.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Repository\UserRepository;
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
     * @return Response
     */
    public function users(UserRepository $repo): Response
    {
        $users = $repo->findAll();

        return $this->render('panel/users.html.twig', [
            'users' => $users
        ]);
    }
}
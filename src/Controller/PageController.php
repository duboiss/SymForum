<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends BaseController
{
    /**
     * @Route("/members", name="page.members")
     * @param UserRepository $repo
     * @return Response
     */
    public function members(UserRepository $repo): Response
    {
        $members = $repo->findAllMembers();

        return $this->render('pages/members.html.twig', [
            'members' => $members
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

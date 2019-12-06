<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Repository\ForumRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class ForumPanelController extends BaseController
{
    /**
     * @Route("/forums", name="panel.forums")
     * @param ForumRepository $forumRepository
     * @return Response
     */
    public function index(ForumRepository $forumRepository): Response
    {
        $forums = $forumRepository->findForumsWithCategories();

        return $this->render('panel/forums.html.twig', [
            'forums' => $forums
        ]);
    }
}
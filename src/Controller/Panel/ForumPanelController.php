<?php

namespace App\Controller\Panel;

use App\Controller\AbstractBaseController;
use App\Repository\ForumRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 * @IsGranted("ROLE_ADMIN")
 */
class ForumPanelController extends AbstractBaseController
{
    /**
     * @Route("/forums", name="panel.forums", methods="GET")
     */
    public function index(ForumRepository $forumRepository): Response
    {
        return $this->render('panel/forum/index.html.twig', [
            'forums' => $forumRepository->findForumsWithCategories(),
        ]);
    }
}

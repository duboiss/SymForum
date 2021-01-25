<?php

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Repository\ForumRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/forums", name="admin.forum.")
 * @IsGranted("ROLE_ADMIN")
 */
class ForumAdminController extends AbstractBaseController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
    public function index(ForumRepository $forumRepository): Response
    {
        return $this->render('admin/forum/index.html.twig', [
            'forums' => $forumRepository->findForumsWithCategories(),
        ]);
    }
}

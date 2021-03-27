<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Repository\ForumRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route(path: '/admin/forums', name: 'admin.forum.')]
class ForumAdminController extends AbstractBaseController
{
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(ForumRepository $forumRepository): Response
    {
        return $this->render('admin/forum/index.html.twig', [
            'forums' => $forumRepository->findForumsWithCategories(),
        ]);
    }
}

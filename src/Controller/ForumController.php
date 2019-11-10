<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Repository\ThreadRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends BaseController
{
    /**
     * @Route("/forums/{id}-{slug}", name="forum.show", requirements={"id"="\d+", "slug"="[\w\-_]+?$"})
     * @param Forum $forum
     * @param ThreadRepository $threadsRepo
     * @return Response
     */
    public function forum(Forum $forum, ThreadRepository $threadsRepo): Response
    {
        $threads = $threadsRepo->findThreadsByForum($forum);

        return $this->render('forums/forum.html.twig', [
            'forum' => $forum,
            'threads' => $threads
        ]);
    }
}

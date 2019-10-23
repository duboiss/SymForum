<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Repository\ForumRepository;
use App\Repository\ThreadRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends BaseController
{
    /**
     * @Route("/forums/{id}-{slug}", name="forum.show", requirements={"id"="\d+", "slug"="[\w\-_]+?$"})
     * @param Forum $forum
     * @param ThreadRepository $threadsRepo
     * @param ForumRepository $forumsRepo
     * @return Response
     */
    public function forum(Forum $forum, ThreadRepository $threadsRepo, ForumRepository $forumsRepo): Response
    {
        $threads = $threadsRepo->findThreadsByForum($forum);
        $subforums = $forumsRepo->findSubforumsByParent($forum);

        return $this->render('forums/forum.html.twig', [
            'forum' => $forum,
            'threads' => $threads,
            'subforums' => $subforums
        ]);
    }
}

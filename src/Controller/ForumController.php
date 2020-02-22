<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Repository\ThreadRepository;
use App\Service\ForumService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends BaseController
{
    /**
     * @Route("/forums/{slug}", name="forum.show", requirements={"id"="\d+", "slug"="[\w\-_]+?$"})
     * @param Forum $forum
     * @param ThreadRepository $threadRepository
     * @return Response
     */
    public function show(Forum $forum, ThreadRepository $threadRepository): Response
    {
        $threads = $threadRepository->findThreadsByForum($forum);

        return $this->render('forums/forum.html.twig', [
            'forum' => $forum,
            'threads' => $threads
        ]);
    }

    /**
     * @Route("/forums/{id}-{slug}/lock", name="forum.lock")
     * @IsGranted("LOCK", subject="forum")
     * @param Forum $forum
     * @param ForumService $forumService
     * @return Response
     */
    public function lock(Forum $forum, ForumService $forumService): Response
    {
        $forumService->lock($forum);
        $this->addCustomFlash('success', 'Forum', 'Le forum a été fermé !');

        return $this->redirectToRoute('forum.show', [
            'slug' => $forum->getSlug()
        ]);
    }

    /**
     * @Route("/forums/{id}-{slug}/unlock", name="forum.unlock")
     * @IsGranted("LOCK", subject="forum")
     * @param Forum $forum
     * @param ForumService $forumService
     * @return Response
     */
    public function unlock(Forum $forum, ForumService $forumService): Response
    {
        $forumService->unlock($forum);
        $this->addCustomFlash('success', 'Forum', 'Le forum a été ouvert !');

        return $this->redirectToRoute('forum.show', [
            'slug' => $forum->getSlug()
        ]);
    }
}

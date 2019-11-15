<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Repository\ThreadRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends BaseController
{
    /**
     * @Route("/forums/{slug}", name="forum.show", requirements={"id"="\d+", "slug"="[\w\-_]+?$"})
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

    /**
     * @Route("/forums/{id}-{slug}/lock", name="forum.lock")
     * @IsGranted("ROLE_MODERATOR")
     * @param Forum $forum
     * @param ObjectManager $manager
     * @return Response
     */
    public function lock(Forum $forum, ObjectManager $manager): Response
    {
        if ($forum->getLocked()) {
            $this->addCustomFlash('error', 'Forum', 'Ce forum est déjà fermé !');
        } else {
            $forum->setLocked(true);
            $manager->flush();

            $this->addCustomFlash('success', 'Forum', 'Le forum a été fermé !');
        }

        return $this->redirectToRoute('forum.show', [
            'slug' => $forum->getSlug()
        ]);
    }

    /**
     * @Route("/forums/{id}-{slug}/unlock", name="forum.unlock")
     * @IsGranted("ROLE_MODERATOR")
     * @param Forum $forum
     * @param ObjectManager $manager
     * @return Response
     */
    public function unlock(Forum $forum, ObjectManager $manager): Response
    {
        if (!$forum->getLocked()) {
            $this->addCustomFlash('error', 'Forum', 'Ce forum est déjà ouvert !');
        } else {
            $forum->setLocked(false);
            $manager->flush();

            $this->addCustomFlash('success', 'Forum', 'Le forum a été ouvert !');
        }

        return $this->redirectToRoute('forum.show', [
            'slug' => $forum->getSlug()
        ]);
    }
}

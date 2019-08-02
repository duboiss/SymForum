<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Forum;
use App\Repository\ForumRepository;
use App\Repository\ThreadRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForumController extends AbstractController
{
    /**
     * @Route("/forums/{category_slug}/{id}-{slug}", name="forum.show")
     * @Entity("category", expr="repository.findOneBy({slug: category_slug})")
     * @param Category $category
     * @param Forum $forum
     * @param ThreadRepository $threadsRepo
     * @param ForumRepository $forumsRepo
     * @return Response
     */
    public function forum(Category $category, Forum $forum, ThreadRepository $threadsRepo, ForumRepository $forumsRepo)
    {
        $threads = $threadsRepo->findBy(['forum' => $forum], ['createdAt' => 'DESC']);
        $subforums = $forumsRepo->findBy(['parent' => $forum], ['position' => 'ASC']);

        return $this->render('forums/forum.html.twig', [
            'forum' => $forum,
            'threads' => $threads,
            'subforums' => $subforums
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Forum;
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
     * @return Response
     */
    public function forum(Category $category, Forum $forum)
    {
        return $this->render('forums/forum.html.twig', [
            'forum' => $forum,
        ]);
    }
}

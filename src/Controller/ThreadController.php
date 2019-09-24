<?php

namespace App\Controller;

use App\Entity\Thread;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThreadController extends AbstractController
{
    /**
     * @Route("/forums/threads/{id}-{slug}", name="forums.thread")
     * @param Thread $thread
     * @param MessageRepository $repo
     * @return Response
     */
    public function index(Thread $thread, MessageRepository $repo)
    {
        $messages = $repo->findMessagesByThreadWithAuthor($thread);

        return $this->render('forums/thread.html.twig', [
            'thread' => $thread,
            'messages' => $messages
        ]);
    }
}

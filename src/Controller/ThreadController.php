<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Thread;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThreadController extends AbstractController
{
    /**
     * @Route("/forums/threads/{id}-{slug}", name="forums.thread")
     * @param Thread $thread
     * @param MessageRepository $repo
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function index(Thread $thread, MessageRepository $repo, Request $request)
    {
        $messages = $repo->findMessagesByThreadWithAuthor($thread);


        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();

            $message->setAuthor($user);
            $message->setThread($thread);

            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('forums.thread', [
                'id' => $thread->getId(),
                'slug' => $thread->getSlug(),
                '_fragment' => $message->getId()
            ]);
        }

        return $this->render('forums/thread.html.twig', [
            'thread' => $thread,
            'messages' => $messages,
            'form' => $form->createView()
        ]);
    }
}

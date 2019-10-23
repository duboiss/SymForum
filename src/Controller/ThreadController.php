<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Thread;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThreadController extends BaseController
{
    /**
     * @Route("/forums/threads/{id}-{slug}", name="thread.show")
     * @param Thread $thread
     * @param MessageRepository $repo
     * @param Request $request
     * @return Response
     */
    public function show(Thread $thread, MessageRepository $repo, Request $request): Response
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

            $this->addCustomFlash('success', 'Message', 'Votre message a bien été posté !');

            return $this->redirectToRoute('thread.show', [
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

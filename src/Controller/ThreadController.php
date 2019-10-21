<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Thread;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThreadController extends AbstractController
{
    /**
     * @Route("/forums/threads/{id}-{slug}", name="thread.show")
     * @param Thread $thread
     * @param MessageRepository $repo
     * @param Request $request
     * @return Response
     */
    public function index(Thread $thread, MessageRepository $repo, Request $request): Response
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

    /**
     * @Route("/forums/messages/{id}/edit", name="forums.message.edit")
     * @param Message $message
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editMessage(Message $message, Request $request): Response
    {
        $thread = $message->getThread();

        $redirectionRoute = $this->redirectToRoute('thread.show', [
            'id' => $thread->getId(),
            'slug' => $thread->getSlug(),
            '_fragment' => $message->getId()
        ]);

        if ($this->getUser() === $message->getAuthor() && $thread->getLocked() === null) {
            $form = $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                return $redirectionRoute;
            }

            return $this->render('forums/message_edit.html.twig', [
                'message' => $message,
                'form' => $form->createView(),
            ]);
        }

        return $redirectionRoute;
    }
}

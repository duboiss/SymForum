<?php

namespace App\Controller;


use App\Entity\Message;
use App\Entity\Thread;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Service\AntispamService;
use App\Service\MessageService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends BaseController
{
    /**
     * @Route("/forums/messages/add/{id}", name="message.add", methods={"POST"})
     * @param Thread $thread
     * @param Request $request
     * @param AntispamService $antispam
     * @param MessageService $messageService
     * @return Response
     */
    public function new(Thread $thread, Request $request, AntispamService $antispam, MessageService $messageService): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($thread->getLocked()) {
            $this->addCustomFlash('error', 'Message', 'Vous ne pouvez pas ajouter votre message, le sujet est verrouillé !');

            return $this->redirectToRoute('thread.show', [
                'slug' => $thread->getSlug()
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$antispam->canPostMessage($user)) {
                $this->addCustomFlash('error', 'Message', 'Vous devez encore attendre un peu avant de pouvoir poster un message !');

                return $this->redirectToRoute('thread.show', [
                    'slug' => $thread->getSlug(),
                    '_fragment' => $thread->getLastMessage()->getId()
                ]);
            }

            $message = $messageService->createMessage($form['content']->getData(), $thread, $user);

            $this->addCustomFlash('success', 'Message', 'Votre message a bien été posté !');

            return $this->redirectToRoute('thread.show', [
                'slug' => $thread->getSlug(),
                '_fragment' => $message->getId()
            ]);
        }

        return $this->redirectToRoute('thread.show', [
            'slug' => $thread->getSlug()
        ]);
    }

    /**
     * @Route("/forums/messages/{id}/edit", name="message.edit")
     * @IsGranted("EDIT", subject="message")
     * @param Message $message
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Message $message, Request $request): Response
    {
        $thread = $message->getThread();

        $route = $this->redirectToRoute('thread.show', [
            'slug' => $thread->getSlug(),
            '_fragment' => $message->getId()
        ]);

        if ($thread->getLocked()) {
            $this->addCustomFlash('error', 'Message', 'Vous ne pouvez pas modifier votre message, le sujet est verrouillé !');
            return $route;
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addCustomFlash('success', 'Message', 'Votre message a bien été modifié !');
            return $route;
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forums/messages/{id}/delete", name="message.delete")
     * @IsGranted("DELETE", subject="message")
     * @param Message $message
     * @param EntityManagerInterface $em
     * @param MessageService $messageService
     * @param MessageRepository $repo
     * @return Response
     */
    public function delete(Message $message, EntityManagerInterface $em, MessageService $messageService, MessageRepository $repo): Response
    {
        $thread = $message->getThread();
        $forum = $thread->getForum();

        $lastMessage = $messageService->deleteMessage($message);

        if (!$lastMessage) {
            $em->remove($thread);
            $em->flush();

            $this->addCustomFlash('success', 'Message', 'Le message ainsi que le thread ont été supprimé !');

            return $this->redirectToRoute('forum.show', [
                'slug' => $forum->getSlug()
            ]);
        }

        $thread->setLastMessage($lastMessage);
        $em->flush();

        $nextMessage = $repo->findNextMessageInThread($message);
        $fragment = $nextMessage ? $nextMessage->getId() : $lastMessage->getId();

        $this->addCustomFlash('success', 'Message', 'Le message a été supprimé !');

        return $this->redirectToRoute('thread.show', [
            'slug' => $thread->getSlug(),
            '_fragment' => $fragment
        ]);
    }
}
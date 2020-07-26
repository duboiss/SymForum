<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Service\MessageService;
use App\Service\ThreadService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/forums")
 */
class MessageController extends BaseController
{
    /**
     * @Route("/messages/{id}", name="message.show", methods={"GET"})
     */
    public function show(Message $message, ThreadService $threadService): Response
    {
        return $this->redirectToRoute('thread.show', [
            'slug' => $message->getThread()->getSlug(),
            'page' => $threadService->getPageOfMessage($message),
            '_fragment' => $message->getId(),
        ]);
    }

    /**
     * @Route("/messages/{id}/edit", name="message.edit", methods={"GET", "POST"})
     * @IsGranted("EDIT", subject="message")
     *
     * @return RedirectResponse|Response
     */
    public function edit(Message $message, Request $request, MessageService $messageService): Response
    {
        $route = $this->redirectToRoute('message.show', ['id' => $message->getId()]);

        if (!$messageService->canEditMessage($message)) {
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
     * @Route("/messages/{id}/delete", name="message.delete", methods={"GET"})
     * @IsGranted("DELETE", subject="message")
     */
    public function delete(Message $message, EntityManagerInterface $em, MessageService $messageService, MessageRepository $messageRepository): Response
    {
        $thread = $message->getThread();
        $forum = $thread->getForum();

        if (!$messageService->canDeleteMessage($message)) {
            return $this->redirectToRoute('thread.show', [
                'slug' => $thread->getSlug(),
            ]);
        }

        $lastMessage = $messageService->deleteMessage($message);

        if (!$lastMessage) {
            $em->remove($thread);
            $em->flush();

            $this->addCustomFlash('success', 'Message', 'Le message ainsi que le thread ont été supprimé !');

            return $this->redirectToRoute('forum.show', [
                'slug' => $forum->getSlug(),
            ]);
        }

        $thread->setLastMessage($lastMessage);
        $em->flush();

        $this->addCustomFlash('success', 'Message', 'Le message a été supprimé !');

        $nextMessage = $messageRepository->findNextMessageInThread($message);

        return $this->redirectToRoute('message.show', [
            'id' => $nextMessage ? $nextMessage->getId() : $lastMessage->getId(),
        ]);
    }
}

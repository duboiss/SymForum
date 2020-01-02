<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
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
     * @Route("/forums/messages/{id}/edit", name="message.edit")
     * @IsGranted("EDIT", subject="message")
     * @param Message $message
     * @param Request $request
     * @param MessageService $messageService
     * @return RedirectResponse|Response
     */
    public function edit(Message $message, Request $request, MessageService $messageService): Response
    {
        $thread = $message->getThread();

        $route = $this->redirectToRoute('thread.show', [
            'slug' => $thread->getSlug(),
            '_fragment' => $message->getId()
        ]);

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
     * @Route("/forums/messages/{id}/delete", name="message.delete")
     * @IsGranted("DELETE", subject="message")
     * @param Message $message
     * @param EntityManagerInterface $em
     * @param MessageService $messageService
     * @param MessageRepository $messageRepository
     * @return Response
     */
    public function delete(Message $message, EntityManagerInterface $em, MessageService $messageService, MessageRepository $messageRepository): Response
    {
        $thread = $message->getThread();
        $forum = $thread->getForum();

        if (!$messageService->canDeleteMessage($message)) {
            return $this->redirectToRoute('thread.show', [
                'slug' => $thread->getSlug()
            ]);
        }

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

        $nextMessage = $messageRepository->findNextMessageInThread($message);
        $fragment = $nextMessage ? $nextMessage->getId() : $lastMessage->getId();

        $this->addCustomFlash('success', 'Message', 'Le message a été supprimé !');

        return $this->redirectToRoute('thread.show', [
            'slug' => $thread->getSlug(),
            '_fragment' => $fragment
        ]);
    }
}

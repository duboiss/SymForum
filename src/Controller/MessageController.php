<?php

declare(strict_types=1);

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

#[Route(path: '/forums/messages', name: 'message.')]
class MessageController extends AbstractBaseController
{
    #[Route(path: '/{uuid}', name: 'show', methods: ['GET'])]
    public function show(Message $message, ThreadService $threadService): Response
    {
        return $this->redirectToRoute('thread.show', [
            'slug' => $message->getThread()->getSlug(),
            'page' => $threadService->getMessagePage($message),
            '_fragment' => $message->getUuidBase32(),
        ]);
    }

    #[IsGranted('EDIT', subject: 'message')]
    #[Route(path: '/{uuid}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Message $message, Request $request, MessageService $messageService): RedirectResponse | Response
    {
        $route = $this->redirectToRoute('message.show', ['uuid' => $message->getUuidBase32()]);

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

        return $this->renderForm('message/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[IsGranted('DELETE', subject: 'message')]
    #[Route(path: '/{uuid}/delete', name: 'delete', methods: ['GET'])]
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
            'uuid' => $nextMessage ? $nextMessage->getUuid() : $lastMessage->getUuid(),
        ]);
    }
}

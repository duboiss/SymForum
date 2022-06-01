<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class MessageService
{
    private FlashBagInterface $flashBag;

    public function __construct(private EntityManagerInterface $em, private MessageRepository $messageRepository, RequestStack $requestStack, private AntispamService $antispamService)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function canPostMessage(Thread $thread, User $user): bool
    {
        if ($thread->isLock()) {
            $this->flashBag->add('error', ['title' => 'Message', 'content' => 'Vous ne pouvez pas ajouter votre message, le sujet est verrouillé !']);

            return false;
        }
        if (!$this->antispamService->canPostMessage($user)) {
            $this->flashBag->add('error', ['title' => 'Message', 'content' => 'Vous devez encore attendre un peu avant de pouvoir poster un message !']);

            return false;
        }

        return true;
    }

    public function canEditMessage(Message $message): bool
    {
        if ($message->getThread()?->isLock()) {
            $this->flashBag->add('error', ['title' => 'Message', 'content' => 'Vous ne pouvez pas éditer votre message, le sujet est verrouillé !']);

            return false;
        }

        return true;
    }

    public function canDeleteMessage(Message $message): bool
    {
        $thread = $message->getThread();

        if (!$thread) {
            return true;
        }

        $firstMessageInThread = $this->messageRepository->findFirstMessageInThread($thread);

        if ($message === $firstMessageInThread && $thread->getTotalMessages() > 1) {
            $this->flashBag->add('error', ['title' => 'Message', 'content' => 'Le premier message ne peut pas être supprimé car le sujet contient des réponses !']);

            return false;
        }

        return true;
    }

    public function createMessage(string $content, Thread $thread): Message
    {
        $message = (new Message())
            ->setContent($content)
            ->setThread($thread)
        ;

        $this->em->persist($message);

        $thread->setLastMessage($message);

        if ($forum = $thread->getForum()) {
            $forum->setLastMessage($message);
        }

        $this->em->flush();

        return $message;
    }

    public function deleteMessage(Message $message): ?Message
    {
        $thread = $message->getThread();
        $forum = $thread?->getForum();

        if ($thread && $thread->getLastMessage() === $message) {
            $thread->setLastMessage(null);
        }

        if ($forum && $forum->getLastMessage() === $message) {
            $forum->setLastMessage(null);
        }

        $this->em->remove($message);
        $this->em->flush();

        if ($forum && !$forum->getLastMessage()) {
            $forum->setLastMessage($this->messageRepository->findLastMessageByForum($forum));
        }

        if ($thread) {
            return $this->messageRepository->findLastMessageByThread($thread);
        }

        return null;
    }

    public function deleteMessagesByUser(User $user): void
    {
        foreach ($user->getMessages() as $message) {
            $this->deleteMessage($message);
        }
    }

    public function setAuthorNullByUser(User $user): void
    {
        if (count($user->getMessages()) > 0) {
            foreach ($user->getMessages() as $message) {
                $message->setAuthor(null);
                $message->setContent('supprimé');
            }

            $this->em->flush();
        }
    }

    public function setUpdatedbyNullByUser(User $user): void
    {
        if (count($user->getUpdatedByMessages()) > 0) {
            foreach ($user->getUpdatedByMessages() as $message) {
                $message->setUpdatedBy(null);
            }

            $this->em->flush();
        }
    }
}

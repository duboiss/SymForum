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
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageService
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(private readonly EntityManagerInterface $em, private readonly MessageRepository $messageRepository, RequestStack $requestStack, private readonly AntispamService $antispamService, private readonly TranslatorInterface $translator)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function canPostMessage(Thread $thread, User $user): bool
    {
        if ($thread->isLock()) {
            $this->flashBag->add('error', [
                'title' => $this->translator->trans('Message'),
                'content' => $this->translator->trans("You can't add your message because the thread is locked"),
            ]);

            return false;
        }
        if (!$this->antispamService->canPostMessage($user)) {
            $this->flashBag->add('error', [
                'title' => $this->translator->trans('Message'),
                'content' => $this->translator->trans('You have to wait a while before you can post a message'),
            ]);

            return false;
        }

        return true;
    }

    public function canEditMessage(Message $message): bool
    {
        if ($message->getThread()?->isLock()) {
            $this->flashBag->add('error', [
                'title' => $this->translator->trans('Message'),
                'content' => $this->translator->trans("You can't edit your message because the thread is locked"),
            ]);

            return false;
        }

        return true;
    }

    public function canDeleteMessage(Message $message): bool
    {
        if ($this->isTheMessageFirstOneInThreadAndThreadHasAnswers($message)) {
            $this->flashBag->add('error', [
                'title' => $this->translator->trans('Message'),
                'content' => $this->translator->trans('The first message cannot be deleted because the thread contains replies'),
            ]);

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
                $message->setContent($this->translator->trans('Message deleted'));
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

    private function isTheMessageFirstOneInThreadAndThreadHasAnswers(Message $message): bool
    {
        if (!$thread = $message->getThread()) {
            return true;
        }

        return $message === $this->messageRepository->findFirstMessageInThread($thread) && $thread->getTotalMessages() > 1;
    }
}

<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ThreadService
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(private readonly EntityManagerInterface $em, private readonly MessageRepository $messageRepository, RequestStack $requestStack, private readonly AntispamService $antispamService, private readonly OptionService $optionService)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function canPostThread(Forum $forum, User $user): bool
    {
        if ($forum->isLock()) {
            $this->flashBag->add('error', ['title' => 'Sujet', 'content' => 'Vous ne pouvez pas ajouter de sujet, le forum est verrouillé !']);

            return false;
        }
        if (!$this->antispamService->canPostThread($user)) {
            $this->flashBag->add('error', ['title' => 'Sujet', 'content' => 'Vous devez encore attendre un peu avant de pouvoir créer un sujet !']);

            return false;
        }

        return true;
    }

    public function createThread(string $title, Forum $forum, bool $lock = false, bool $pin = false): Thread
    {
        $thread = (new Thread())
            ->setTitle($title)
            ->setForum($forum)
            ->setLock($lock)
            ->setPin($pin)
        ;

        $this->em->persist($thread);
        $this->em->flush();

        return $thread;
    }

    public function deleteThread(Thread $thread): void
    {
        $forum = $thread->getForum();
        $lastMessage = $thread->getLastMessage();

        if ($forum && $forum->getLastMessage() === $lastMessage) {
            $forum->setLastMessage(null);
        }

        $thread->setLastMessage(null);

        foreach ($thread->getMessages() as $message) {
            $this->em->remove($message);
        }

        $this->em->flush();
        $this->em->remove($thread);
        $this->em->flush();

        if ($forum && !$forum->getLastMessage()) {
            $forum->setLastMessage($this->messageRepository->findLastMessageByForum($forum));
            $this->em->flush();
        }
    }

    public function lock(Thread $thread): void
    {
        $thread->setLock(true);
        $this->em->flush();
    }

    public function unlock(Thread $thread): void
    {
        $thread->setLock(false);
        $this->em->flush();
    }

    public function pin(Thread $thread): void
    {
        $thread->setPin(true);
        $this->em->flush();
    }

    public function unpin(Thread $thread): void
    {
        $thread->setPin(false);
        $this->em->flush();
    }

    public function deleteThreadsByUser(User $user): void
    {
        foreach ($user->getThreads() as $thread) {
            $this->deleteThread($thread);
        }
    }

    public function setAuthorNullByUser(User $user): void
    {
        if (count($user->getThreads()) > 0) {
            foreach ($user->getThreads() as $thread) {
                $thread->setAuthor(null);
            }

            $this->em->flush();
        }
    }

    public function getMessagePage(Message $message): int
    {
        if (!$thread = $message->getThread()) {
            throw new \Exception('No thread found');
        }

        $messages = $this->messageRepository->findMessagesByThread($thread, true);
        $key = array_search($message->getId(), $messages, true);
        $messagesPerThread = (int) $this->optionService->get('messages_per_thread', '10');

        return (int) (ceil(((int) $key + 1) / $messagesPerThread));
    }
}

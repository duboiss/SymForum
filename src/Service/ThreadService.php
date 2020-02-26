<?php

namespace App\Service;

use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ThreadService
{
    const TOTAL_MESSAGES_BY_THREAD = 10;

    /** @var EntityManagerInterface */
    private $em;

    /** @var MessageRepository */
    private $messageRepository;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var AntispamService */
    private $antispamService;

    /* @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(EntityManagerInterface $em, MessageRepository $messageRepository, FlashBagInterface $flashBag, AntispamService $antispamService, UrlGeneratorInterface $urlGenerator)
    {
        $this->em = $em;
        $this->messageRepository = $messageRepository;
        $this->flashBag = $flashBag;
        $this->antispamService = $antispamService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Forum $forum
     * @param User $user
     * @return bool
     */
    public function canPostThread(Forum $forum, User $user): bool
    {
        if ($forum->isLock()) {
            $this->flashBag->add('error', ['title' => 'Sujet', 'content' => 'Vous ne pouvez pas ajouter de sujet, le forum est verrouillé !']);
            return false;
        } elseif (!$this->antispamService->canPostThread($user)) {
            $this->flashBag->add('error', ['title' => 'Sujet', 'content' => 'Vous devez encore attendre un peu avant de pouvoir créer un sujet !']);
            return false;
        }

        return true;
    }

    /**
     * @param string $title
     * @param Forum $forum
     * @param User $user
     * @param bool $lock
     * @param bool $pin
     * @return Thread
     */
    public function createThread(string $title, Forum $forum, User $user, bool $lock = false, bool $pin = false): Thread
    {
        $thread = (new Thread())
            ->setTitle($title)
            ->setAuthor($user)
            ->setForum($forum)
            ->setIsLock($lock)
            ->setIsPin($pin);

        $thread->getForum()->incrementTotalThreads();

        $this->em->persist($thread);
        $this->em->flush();

        return $thread;
    }

    /**
     * @param Thread $thread
     * @return void
     */
    public function deleteThread(Thread $thread): void
    {
        $forum = $thread->getForum();
        $lastMessage = $thread->getLastMessage();

        if ($forum->getLastMessage() === $lastMessage) {
            $forum->setLastMessage(null);
        }

        $thread->setLastMessage(null);

        foreach ($thread->getMessages() as $message) {
            $this->em->remove($message);
            $forum->decrementTotalMessages();
        }

        $this->em->flush();

        $forum->decrementTotalThreads();

        $this->em->remove($thread);
        $this->em->flush();

        if (!$forum->getLastMessage()) {
            $forum->setLastMessage($this->messageRepository->findLastMessageByForum($forum));
            $this->em->flush();
        }
    }

    /**
     * @param Thread $thread
     */
    public function lock(Thread $thread): void
    {
        $thread->setIsLock(true);
        $this->em->flush();
    }

    /**
     * @param Thread $thread
     */
    public function unlock(Thread $thread): void
    {
        $thread->setIsLock(false);
        $this->em->flush();
    }

    /**
     * @param Thread $thread
     */
    public function pin(Thread $thread): void
    {
        $thread->setIsPin(true);
        $this->em->flush();
    }

    /**
     * @param Thread $thread
     */
    public function unpin(Thread $thread): void
    {
        $thread->setIsPin(false);
        $this->em->flush();
    }

    /**
     * @param User $user
     */
    public function deleteThreadsByUser(User $user): void
    {
        foreach ($user->getThreads() as $thread) {
            $this->deleteThread($thread);
        }
    }

    /**
     * @param User $user
     */
    public function setAuthorNullByUser(User $user): void
    {
        if (count($user->getThreads()) > 0) {
            foreach ($user->getThreads() as $thread) {
                $thread->setAuthor(null);
            }

            $this->em->flush();
        }
    }

    /**
     * @param Message $message
     * @return int
     */
    public function getPageOfMessage(Message $message): int
    {
        $messages = $this->messageRepository->findBy(['thread' => $message->getThread()], ['publishedAt' => 'ASC']);
        $key = array_search($message, $messages);

        return (ceil(($key + 1) / self::TOTAL_MESSAGES_BY_THREAD));
    }

    /**
     * @param Message $message
     * @return string
     */
    public function getMessageLink(Message $message): string
    {
        $thread = $message->getThread();
        $page = $this->getPageOfMessage($message);

        return $this->urlGenerator->generate('thread.show', [
            'slug' => $thread->getSlug(),
            'page' => $page,
            '_fragment' => $message->getId()
        ]);
    }
}

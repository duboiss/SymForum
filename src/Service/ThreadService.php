<?php

namespace App\Service;

use App\Entity\Forum;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class ThreadService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var MessageRepository
     */
    private $messageRepository;

    public function __construct(EntityManagerInterface $em, MessageRepository $messageRepository)
    {
        $this->em = $em;
        $this->messageRepository = $messageRepository;
    }

    /**
     * @param string $title
     * @param Forum $forum
     * @param User $user
     * @return Thread
     */
    public function createThread(string $title, Forum $forum, User $user): Thread
    {
        $thread = new Thread();

        $thread->setTitle($title)
            ->setAuthor($user)
            ->setForum($forum)
            ->setLocked(false);

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
        $this->em->remove($lastMessage);

        foreach ($thread->getMessages() as $message) {
            $this->em->remove($message);
        }

        $this->em->flush();

        $this->em->remove($thread);
        $this->em->flush();

        if (!$forum->getLastMessage()) {
            $forum->setLastMessage($this->messageRepository->findLastMessageByForum($forum));
            $this->em->flush();
        }
    }
}
<?php

namespace App\Service;

use App\Entity\Forum;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\Common\Persistence\ObjectManager;

class ThreadService
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var MessageRepository
     */
    private $messageRepository;

    public function __construct(ObjectManager $manager, MessageRepository $messageRepository)
    {
        $this->manager = $manager;
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

        $this->manager->persist($thread);
        $this->manager->flush();

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
        $this->manager->remove($lastMessage);

        foreach ($thread->getMessages() as $message) {
            $this->manager->remove($message);
        }

        $this->manager->flush();

        $this->manager->remove($thread);
        $this->manager->flush();

        if (!$forum->getLastMessage()) {
            $forum->setLastMessage($this->messageRepository->findLastMessageByForum($forum));
            $this->manager->flush();
        }
    }
}
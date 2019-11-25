<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\Common\Persistence\ObjectManager;

class MessageService
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
     * @param string $content
     * @param Thread $thread
     * @param User $user
     * @return Message
     */
    public function createMessage(string $content, Thread $thread, User $user): Message
    {
        $message = new Message();

        $message->setAuthor($user)
            ->setContent($content)
            ->setThread($thread);

        $this->manager->persist($message);
        $thread->setLastMessage($message);
        $thread->getForum()->setLastMessage($message);
        $this->manager->flush();

        return $message;
    }

    /**
     * @param Message $message
     * @return Message|null
     */
    public function deleteMessage(Message $message): ?Message
    {
        $thread = $message->getThread();
        $forum = $thread->getForum();

        if ($thread->getLastMessage() === $message) {
            $thread->setLastMessage(null);
        }

        if ($forum->getLastMessage() === $message) {
            $forum->setLastMessage(null);
        }

        $this->manager->remove($message);
        $this->manager->flush();

        if (!$forum->getLastMessage()) {
            $forum->setLastMessage($this->messageRepository->findLastMessageByForum($forum));
        }

        return $this->messageRepository->findLastMessageByThread($thread);
    }
}

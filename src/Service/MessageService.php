<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class MessageService
{
    /** @var EntityManagerInterface */
    private $manager;

    /** @var MessageRepository */
    private $messageRepository;

    public function __construct(EntityManagerInterface $em, MessageRepository $messageRepository)
    {
        $this->manager = $em;
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
        $thread->setTotalMessages($thread->getTotalMessages() + 1);

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
        $thread->setTotalMessages($thread->getTotalMessages() - 1);
        $this->manager->flush();

        if (!$forum->getLastMessage()) {
            $forum->setLastMessage($this->messageRepository->findLastMessageByForum($forum));
        }

        return $this->messageRepository->findLastMessageByThread($thread);
    }
}

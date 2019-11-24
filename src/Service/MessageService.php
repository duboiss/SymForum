<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class MessageService
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
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
}

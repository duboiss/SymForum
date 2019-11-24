<?php

namespace App\Service;

use App\Entity\Forum;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class ThreadService
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
}
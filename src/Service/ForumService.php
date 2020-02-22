<?php

namespace App\Service;

use App\Entity\Forum;
use Doctrine\ORM\EntityManagerInterface;

class ForumService
{
    /* @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Forum $forum
     */
    public function lock(Forum $forum): void
    {
        $forum->setIsLock(true);
        $this->em->flush();
    }

    /**
     * @param Forum $forum
     */
    public function unlock(Forum $forum): void
    {
        $forum->setIsLock(false);
        $this->em->flush();
    }
}
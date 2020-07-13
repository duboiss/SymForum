<?php

namespace App\Service;

use App\Entity\Forum;
use Doctrine\ORM\EntityManagerInterface;

class ForumService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function lock(Forum $forum): void
    {
        $forum->setLock(true);
        $this->em->flush();
    }

    public function unlock(Forum $forum): void
    {
        $forum->setLock(false);
        $this->em->flush();
    }
}

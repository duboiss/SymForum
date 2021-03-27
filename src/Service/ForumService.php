<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Forum;
use Doctrine\ORM\EntityManagerInterface;

class ForumService
{
    public function __construct(private EntityManagerInterface $em)
    {
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

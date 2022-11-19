<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Thread;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

#[AsEntityListener(event: 'prePersist', entity: Thread::class)]
#[AsEntityListener(event: 'preRemove', entity: Thread::class)]
class ThreadEntityListener
{
    public function prePersist(Thread $thread): void
    {
        $thread->getForum()?->incrementTotalThreads();
    }

    public function preRemove(Thread $thread): void
    {
        $thread->getForum()?->decrementTotalThreads();
    }
}

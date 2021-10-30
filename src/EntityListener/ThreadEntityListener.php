<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Thread;

class ThreadEntityListener
{
    public function prePersist(Thread $thread): void
    {
        $thread->getForum()->incrementTotalThreads();
    }

    public function preRemove(Thread $thread): void
    {
        $thread->getForum()->decrementTotalThreads();
    }
}

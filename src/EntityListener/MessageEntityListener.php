<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Message;

class MessageEntityListener
{
    public function prePersist(Message $message): void
    {
        if (!$thread = $message->getThread()) {
            return;
        }

        $thread->incrementTotalMessages();
        $thread->getForum()?->incrementTotalMessages();
    }

    public function preRemove(Message $message): void
    {
        if (!$thread = $message->getThread()) {
            return;
        }

        $thread->decrementTotalMessages();
        $thread->getForum()?->decrementTotalMessages();
    }
}

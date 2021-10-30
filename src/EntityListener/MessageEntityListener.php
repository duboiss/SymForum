<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Message;

class MessageEntityListener
{
    public function prePersist(Message $message): void
    {
        $thread = $message->getThread();
        $thread->incrementTotalMessages();
        $thread->getForum()->incrementTotalMessages();
    }

    public function preRemove(Message $message): void
    {
        $thread = $message->getThread();
        $thread->decrementTotalMessages();
        $thread->getForum()->decrementTotalMessages();
    }
}

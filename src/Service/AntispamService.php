<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use DateTime;

class AntispamService
{
    // TODO Replace constant(s) by CoreOption objects
    const DELAY_MESSAGE = 60;

    /**
     * @var ThreadRepository
     */
    private $threadsRepo;

    /**
     * @var MessageRepository
     */
    private $messagesRepo;

    public function __construct(ThreadRepository $threadsRepo, MessageRepository $messagesRepo)
    {

        $this->threadsRepo = $threadsRepo;
        $this->messagesRepo = $messagesRepo;
    }

    public function canPostMessage(User $user): bool
    {
        $lastMessage = $this->messagesRepo->findLastMessageByUser($user);
        $currentDate = new DateTime();

        return $currentDate > $lastMessage->getPublishedAt()->modify('+' . self::DELAY_MESSAGE . ' seconds');
    }

    // TODO canPostThread(User $user): bool
}

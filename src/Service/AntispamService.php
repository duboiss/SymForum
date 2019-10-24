<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use DateTime;
use Symfony\Component\Security\Core\Security;

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

    /**
     * @var Security
     */
    private $security;

    public function __construct(ThreadRepository $threadsRepo, MessageRepository $messagesRepo, Security $security)
    {

        $this->threadsRepo = $threadsRepo;
        $this->messagesRepo = $messagesRepo;
        $this->security = $security;
    }

    public function canPostMessage(User $user): bool
    {
        if ($this->security->isGranted('ROLE_MODERATOR')) return true;

        $lastMessage = $this->messagesRepo->findLastMessageByUser($user);
        $currentDate = new DateTime();

        if ($lastMessage) {
            return $currentDate > $lastMessage->getPublishedAt()->modify('+' . self::DELAY_MESSAGE . ' seconds');
        }

        return true;
    }

    // TODO canPostThread(User $user): bool
}

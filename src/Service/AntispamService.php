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
    const DELAY_THREAD = 90;

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

    public function canPostThread(User $user): bool
    {
        $lastThread = $this->threadsRepo->findLastThreadByUser($user);

        if ($lastThread && !$this->security->isGranted('ROLE_MODERATOR')) {
            $currentDate = new DateTime();
            return $currentDate > $lastThread->getCreatedAt()->modify('+' . self::DELAY_THREAD . ' seconds');
        }

        return true;
    }

    public function canPostMessage(User $user): bool
    {
        $lastMessage = $this->messagesRepo->findLastMessageByUser($user);

        if ($lastMessage && !$this->security->isGranted('ROLE_MODERATOR')) {
            $currentDate = new DateTime();
            return $currentDate > $lastMessage->getPublishedAt()->modify('+' . self::DELAY_MESSAGE . ' seconds');
        }

        return true;
    }
}

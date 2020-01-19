<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use DateTime;
use Symfony\Component\Security\Core\Security;

class AntispamService
{
    const DELAY_MESSAGE = 60;
    const DELAY_THREAD = 90;

    /** @var ThreadRepository */
    private $threadRepository;

    /** @var MessageRepository */
    private $messageRepository;

    /** @var Security */
    private $security;

    public function __construct(ThreadRepository $threadRepository, MessageRepository $messageRepository, Security $security)
    {

        $this->threadRepository = $threadRepository;
        $this->messageRepository = $messageRepository;
        $this->security = $security;
    }

    public function canPostThread(User $user): bool
    {
        $lastThread = $this->threadRepository->findLastThreadByUser($user);

        if ($lastThread && !$this->security->isGranted('ROLE_MODERATOR')) {
            $currentDate = new DateTime();
            return $currentDate->modify(sprintf('-%s seconds', self::DELAY_THREAD)) > $lastThread->getCreatedAt();
        }

        return true;
    }

    public function canPostMessage(User $user): bool
    {
        $lastMessage = $this->messageRepository->findLastMessageByUser($user);

        if ($lastMessage && !$this->security->isGranted('ROLE_MODERATOR')) {
            $currentDate = new DateTime();
            return $currentDate->modify(sprintf('-%s seconds', self::DELAY_MESSAGE)) > $lastMessage->getPublishedAt();
        }

        return true;
    }
}

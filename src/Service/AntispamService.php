<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use DateTime;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;

class AntispamService
{
    private const DELAY_MESSAGE = 60;

    private const DELAY_THREAD = 90;

    public function __construct(private readonly ThreadRepository $threadRepository, private readonly MessageRepository $messageRepository, private readonly Security $security)
    {
    }

    /**
     * @throws Exception
     */
    public function canPostThread(User $user): bool
    {
        $lastThread = $this->threadRepository->findLastThreadByUser($user);

        if ($lastThread && !$this->security->isGranted('ROLE_MODERATOR')) {
            $currentDate = new DateTime();

            return $currentDate->modify(sprintf('-%s seconds', self::DELAY_THREAD)) > $lastThread->getCreatedAt();
        }

        return true;
    }

    /**
     * @throws Exception
     */
    public function canPostMessage(User $user): bool
    {
        $lastMessage = $this->messageRepository->findLastMessageByUser($user);

        if ($lastMessage && !$this->security->isGranted('ROLE_MODERATOR')) {
            $currentDate = new DateTime();

            return $currentDate->modify(sprintf('-%s seconds', self::DELAY_MESSAGE)) > $lastMessage->getCreatedAt();
        }

        return true;
    }
}

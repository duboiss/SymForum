<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ThreadService */
    private $threadService;

    /** @var MessageService */
    private $messageService;

    /** @var ReportService */
    private $reportService;

    public function __construct(EntityManagerInterface $em, ThreadService $threadService, MessageService $messageService, ReportService $reportService)
    {
        $this->em = $em;
        $this->threadService = $threadService;
        $this->messageService = $messageService;
        $this->reportService = $reportService;
    }

    /**
     * @param User $user
     * @param bool $deleteContent
     * @return void
     */
    public function deleteUser(User $user, $deleteContent = false): void
    {
        $deleteContent ? $this->resetUser($user) : $this->setContentNullByUser($user);
        $this->reportService->setTreatedbyNullByUser($user);

        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @return void
     */
    public function resetUser(User $user): void
    {
        $this->threadService->deleteThreadsByUser($user);
        $this->messageService->deleteMessagesByUser($user);
        $this->reportService->deleteReportsByUser($user);
    }

    /**
     * @param User $user
     * @return void
     */
    public function setContentNullByUser(User $user): void
    {
        $this->threadService->setAuthorNullByUser($user);
        $this->messageService->setAuthorNullByUser($user);
        $this->reportService->setAuthorNullByUser($user);
        $this->reportService->setTreatedbyNullByUser($user);
    }
}

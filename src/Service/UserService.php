<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    public function __construct(private EntityManagerInterface $em, private ThreadService $threadService, private MessageService $messageService, private ReportService $reportService)
    {
    }

    public function deleteUser(User $user, bool $deleteContent = false): void
    {
        $deleteContent ? $this->resetUser($user) : $this->setContentNullByUser($user);
        $this->reportService->setTreatedbyNullByUser($user);
        $this->messageService->setUpdatedbyNullByUser($user);

        $this->em->remove($user);
        $this->em->flush();
    }

    public function resetUser(User $user): void
    {
        $this->threadService->deleteThreadsByUser($user);
        $this->messageService->deleteMessagesByUser($user);
        $this->reportService->deleteReportsByUser($user);
    }

    public function setContentNullByUser(User $user): void
    {
        $this->threadService->setAuthorNullByUser($user);
        $this->messageService->setAuthorNullByUser($user);
        $this->reportService->setAuthorNullByUser($user);
        $this->reportService->setTreatedbyNullByUser($user);
    }
}

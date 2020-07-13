<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\Report;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ReportService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createReport(Message $message, string $reason): void
    {
        $report = (new Report())
            ->setMessage($message)
            ->setReason($reason);

        $this->em->persist($report);
        $this->em->flush();
    }

    public function deleteReport(Report $report): void
    {
        $this->em->remove($report);
        $this->em->flush();
    }

    public function closeReport(Report $report): void
    {
        $report->setTreatedAt(new DateTime());
        $this->em->flush();
    }

    public function deleteReportsByUser(User $user): void
    {
        if (count($user->getReports()) > 0) {
            foreach ($user->getReports() as $report) {
                $this->em->remove($report);
            }

            $this->em->flush();
        }
    }

    public function setAuthorNullByUser(User $user): void
    {
        if (count($user->getReports()) > 0) {
            foreach ($user->getReports() as $report) {
                $report->setReportedBy(null);
            }

            $this->em->flush();
        }
    }

    public function setTreatedbyNullByUser(User $user): void
    {
        if (count($user->getTreatedReports()) > 0) {
            foreach ($user->getTreatedReports() as $report) {
                $report->setTreatedBy(null);
            }

            $this->em->flush();
        }
    }
}

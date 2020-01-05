<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ReportService
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param User $user
     * @return void
     */
    public function deleteReportsByUser(User $user): void
    {
        if (count($user->getReports()) > 0) {
            foreach ($user->getReports() as $report) {
                $this->em->remove($report);
            }

            $this->em->flush();
        }
    }

    /**
     * @param User $user
     * @return void
     */
    public function setAuthorNullByUser(User $user): void
    {
        if (count($user->getReports()) > 0) {
            foreach ($user->getReports() as $report) {
                $report->setReportedBy(null);
            }

            $this->em->flush();
        }
    }

    /**
     * @param User $user
     * @return void
     */
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

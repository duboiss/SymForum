<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Report;
use App\Repository\ReportRepository;
use App\Service\ReportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/forums/reports', name: 'report.')]
class ReportController extends AbstractBaseController
{
    #[IsGranted('REPORT', subject: 'message')]
    #[Route(path: '/{uuid}', name: 'message', methods: ['POST'])]
    public function message(Message $message, Request $request, ReportService $reportService, ReportRepository $reportRepository): Response
    {
        $reason = trim($this->jsonDecodeRequestContent($request)['reason']);

        if ($reportRepository->findOneBy(['message' => $message, 'reportedBy' => $this->getUser()])) {
            return $this->json([
                'message' => 'Vous avez déjà signalé ce message !',
            ], 409);
        }

        if (mb_strlen($reason) < Report::REASON_MIN_LENGTH) {
            return $this->json([
                'message' => 'Merci d\'apporter plus de précisions..',
            ], 400);
        }

        if ($this->getUser() === $message->getAuthor()) {
            return $this->json([
                'message' => 'Vous ne pouvez pas vous signaler vous-même !',
            ], 403);
        }

        $reportService->createReport($message, $reason);

        return $this->json(['message' => 'Le message a été signalé, merci !']);
    }
}

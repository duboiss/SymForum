<?php

namespace App\Controller;

use App\Entity\Message;
use App\Service\ReportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/forums")
 */
class ReportController extends BaseController
{
    /**
     * @Route("/report/{id}", name="report.message", methods={"POST"})
     * @IsGranted("REPORT", subject="message")
     */
    public function message(Message $message, Request $request, ReportService $reportService): Response
    {
        $reason = $this->jsonDecodeRequestContent($request)['reason'];

        if (!$reason) {
            return $this->json([
                'message' => 'Vous devez indiquer un motif !',
            ], 403);
        }

        if (mb_strlen($reason) < 8) {
            return $this->json([
                'message' => 'Merci d\'apporter plus de précisions..',
            ], 403);
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

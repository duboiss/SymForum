<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Report;
use App\Repository\ReportRepository;
use App\Service\ReportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/forums/reports', name: 'report.')]
class ReportController extends AbstractBaseController
{
    public function __construct(private readonly RequestStack $requestStack, private DecoderInterface $decoder, private readonly TranslatorInterface $translator)
    {
        parent::__construct($requestStack, $this->decoder);
    }

    #[IsGranted('REPORT', subject: 'message')]
    #[Route(path: '/{uuid}', name: 'message', methods: ['POST'])]
    public function message(Message $message, Request $request, ReportService $reportService, ReportRepository $reportRepository): Response
    {
        $reason = trim($this->jsonDecodeRequestContent($request)['reason']);

        if ($reportRepository->findOneBy(['message' => $message, 'reportedBy' => $this->getUser()])) {
            return $this->json([
                'message' => $this->translator->trans('You have already reported this message'),
            ], 409);
        }

        if (mb_strlen($reason) < Report::REASON_MIN_LENGTH) {
            return $this->json([
                'message' => $this->translator->trans('Please provide more details'),
            ], 400);
        }

        if ($this->getUser() === $message->getAuthor()) {
            return $this->json([
                'message' => $this->translator->trans("You can't report yourself!"),
            ], 403);
        }

        $reportService->createReport($message, $reason);

        return $this->json(['message' => $this->translator->trans('The message has been reported')]);
    }
}

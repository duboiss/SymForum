<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Entity\Report;
use App\Repository\ReportRepository;
use App\Service\ReportService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/reports', name: 'admin.report.')]
class ReportAdminController extends AbstractBaseController
{
    public function __construct(private readonly RequestStack $requestStack, private DecoderInterface $decoder, private readonly TranslatorInterface $translator)
    {
        parent::__construct($requestStack, $this->decoder);
    }

    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(ReportRepository $reportRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $reportRepository->findAllReportsQb(),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('admin/report/index.html.twig', [
            'pagination' => $pagination,
            'nbUntreatedReports' => $reportRepository->countUntreatedReports(),
        ]);
    }

    #[Route(path: '/{uuid}', name: 'show', methods: ['GET'])]
    public function show(Report $report, ReportRepository $reportRepository): Response
    {
        return $this->render('admin/report/show.html.twig', [
            'report' => $report,
            'messageReports' => $report->getMessage() ? $reportRepository->findByMessage($report->getMessage(), $report) : null,
        ]);
    }

    #[Route(path: '/{uuid}/delete', name: 'delete', methods: ['GET'])]
    public function delete(Report $report, ReportService $reportService): Response
    {
        $reportService->deleteReport($report);
        $this->addCustomFlash('success', $this->translator->trans('Report'), $this->translator->trans('The report has been deleted'));

        return $this->redirectToRoute('admin.report.index');
    }

    #[Route(path: '/{uuid}/close', name: 'close', methods: ['GET'])]
    public function close(Report $report, ReportService $reportService): Response
    {
        $reportService->closeReport($report);
        $this->addCustomFlash('success', $this->translator->trans('Report'), $this->translator->trans('The report has been closed'));

        return $this->redirectToRoute('admin.report.index');
    }
}

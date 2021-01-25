<?php

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Entity\Report;
use App\Repository\ReportRepository;
use App\Service\ReportService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/reports", name="admin.report.")
 */
class ReportAdminController extends AbstractBaseController
{
    /**
     * @Route("/", name="index", methods="GET")
     */
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

    /**
     * @Route("/{id}", name="show", methods="GET")
     */
    public function show(Report $report, ReportRepository $reportRepository): Response
    {
        return $this->render('admin/report/show.html.twig', [
            'report' => $report,
            'messageReports' => $reportRepository->findByMessage($report->getMessage(), $report),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete", methods="GET")
     */
    public function delete(Report $report, ReportService $reportService): Response
    {
        $reportService->deleteReport($report);
        $this->addCustomFlash('success', 'Signalement', 'Le signalement a été supprimé !');

        return $this->redirectToRoute('admin.report.index');
    }

    /**
     * @Route("/{id}/close", name="close", methods="GET")
     */
    public function close(Report $report, ReportService $reportService): Response
    {
        $reportService->closeReport($report);
        $this->addCustomFlash('success', 'Signalement', 'Le signalement a été clôturé !');

        return $this->redirectToRoute('admin.report.index');
    }
}

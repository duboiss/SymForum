<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Repository\ReportRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class ReportPanelController extends BaseController
{
    /**
     * @Route("/reports", name="panel.reports")
     * @param ReportRepository $reportRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(ReportRepository $reportRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $reports = $reportRepository->findAllReportsQb();
        $nbUntreatedReports = $reportRepository->countUntreatedReports();

        $pagination = $paginator->paginate(
            $reports,
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('panel/reports.html.twig', [
            'pagination' => $pagination,
            'nbUntreatedReports' => $nbUntreatedReports
        ]);
    }
}
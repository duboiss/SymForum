<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Repository\ReportRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class ReportPanelController extends BaseController
{
    /**
     * @Route("/reports", name="panel.reports")
     * @param ReportRepository $repo
     * @return Response
     */
    public function reports(ReportRepository $repo): Response
    {
        $reports = $repo->findAll();
        $nbUntreatedReports = $repo->countUntreatedReports();

        return $this->render('panel/reports.html.twig', [
            'reports' => $reports,
            'nbUntreatedReports' => $nbUntreatedReports
        ]);
    }
}
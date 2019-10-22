<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanelController extends AbstractController
{
    /**
     * @Route("/panel", name="panel.homepage")
     * @param ReportRepository $repo
     * @return Response
     */
    public function panel(ReportRepository $repo): Response
    {
        $nbUntreatedReports = $repo->countUntreatedReports();
        return $this->render('panel/homepage.html.twig', [
            'nbUntreatedReports' => $nbUntreatedReports
        ]);
    }

    /**
     * @Route("/panel/reports", name="panel.reports")
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

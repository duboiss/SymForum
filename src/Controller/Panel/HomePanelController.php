<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Repository\ReportRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class HomePanelController extends BaseController
{
    /**
     * @Route("", name="panel.homepage")
     * @param ReportRepository $reportRepository
     * @return Response
     */
    public function homepage(ReportRepository $reportRepository): Response
    {
        $nbUntreatedReports = $reportRepository->countUntreatedReports();

        return $this->render('panel/homepage.html.twig', [
            'nbUntreatedReports' => $nbUntreatedReports
        ]);
    }
}

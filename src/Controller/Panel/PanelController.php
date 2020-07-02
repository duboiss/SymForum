<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Repository\ReportRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class PanelController extends BaseController
{
    /**
     * @Route("", name="panel.homepage", methods={"GET"})
     * @param ReportRepository $reportRepository
     * @return Response
     */
    public function homepage(ReportRepository $reportRepository): Response
    {
        return $this->render('panel/homepage.html.twig', [
            'nbUntreatedReports' => $reportRepository->countUntreatedReports()
        ]);
    }
}

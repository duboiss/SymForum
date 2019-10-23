<?php

namespace App\Controller;

use App\Repository\ReportRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panel")
 */
class PanelController extends BaseController
{
    /**
     * @Route("", name="panel.homepage")
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

    /**
     * @Route("/users", name="panel.users")
     * @param UserRepository $repo
     * @return Response
     */
    public function users(UserRepository $repo): Response
    {
        $users = $repo->findAll();

        return $this->render('panel/users.html.twig', [
            'users' => $users
        ]);
    }
}

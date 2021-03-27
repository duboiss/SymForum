<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Repository\ReportRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractBaseController
{
    #[Route(path: '/admin', name: 'admin.homepage', methods: ['GET'])]
    public function homepage(ReportRepository $reportRepository): Response
    {
        return $this->render('admin/homepage.html.twig', [
            'nbUntreatedReports' => $reportRepository->countUntreatedReports(),
        ]);
    }
}

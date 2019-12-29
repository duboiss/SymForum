<?php

namespace App\Controller\Panel;

use App\Controller\BaseController;
use App\Entity\Report;
use App\Repository\ReportRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

        return $this->render('panel/reports/index.html.twig', [
            'pagination' => $pagination,
            'nbUntreatedReports' => $nbUntreatedReports
        ]);
    }

    /**
     * @Route("/reports/{id}", name="panel.report.show")
     * @param Report $report
     * @param ReportRepository $reportRepository
     * @return Response
     */
    public function show(Report $report, ReportRepository $reportRepository): Response
    {
        $messageReports = $reportRepository->findByMessage($report->getMessage(), $report->getId());

        return $this->render('panel/reports/show.html.twig', [
            'report' => $report,
            'messageReports' => $messageReports
        ]);
    }

    /**
     * @Route("/reports/{id}/delete", name="panel.report.delete")
     * @param Report $report
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(Report $report, EntityManagerInterface $em): Response
    {
        $em->remove($report);
        $em->flush();

        $this->addCustomFlash('success', 'Signalement', 'Le signalement a été supprimé !');

        return $this->redirectToRoute('panel.reports');
    }

    /**
     * @Route("/reports/{id}/close", name="panel.report.close")
     * @param Report $report
     * @param EntityManagerInterface $em
     * @return Response
     * @throws Exception
     */
    public function close(Report $report, EntityManagerInterface $em): Response
    {
        $report->setTreatedAt(new DateTime())
            ->setTreatedBy($this->getUser());

        $em->flush();

        $this->addCustomFlash('success', 'Signalement', 'Le signalement a été clôturé !');

        return $this->redirectToRoute('panel.reports');
    }
}

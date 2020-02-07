<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Report;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends BaseController
{
    /**
     * @Route("/forums/report/{id}", name="report.message", methods={"POST"})
     * @IsGranted("REPORT", subject="message")
     * @param Message $message
     * @param EntityManagerInterface $em
     * @param Request $request
     * @return JsonResponse
     */
    public function message(Message $message, EntityManagerInterface $em, Request $request): Response
    {
        $reason = $request->request->get('reason');

        if (!$reason) {
            return $this->json([
                'message' => 'Vous devez indiquer un motif !'
            ], 403);
        } elseif (strlen($reason) < 8) {
            return $this->json([
                'message' => 'Merci d\'apporter plus de précisions..'
            ], 403);
        }

        $report = (new Report())
            ->setMessage($message)
            ->setReason($reason)
            ->setReportedBy($this->getUser());

        $em->persist($report);
        $em->flush();

        return $this->json([
            'message' => 'Le message a été signalé, merci !'
        ], 200);
    }
}

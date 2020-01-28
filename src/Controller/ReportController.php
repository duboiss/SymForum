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
     * @throws Exception
     */
    public function message(Message $message, EntityManagerInterface $em, Request $request): Response
    {
        $submittedToken = $request->request->get('token-report');

        if ($this->isCsrfTokenValid('report-message', $submittedToken)) {

            if (!$request->request->get('reason')) {
                $this->addCustomFlash('error', 'Signalement', 'Vous devez indiquer un motif !');
                return $this->redirectToRoute('thread.show', [
                    'slug' => $message->getThread()->getSlug(),
                    '_fragment' => $message->getId()
                ]);
            }

            $report = (new Report())
                ->setMessage($message)
                ->setReason($request->request->get('reason'))
                ->setReportedBy($this->getUser());

            $em->persist($report);
            $em->flush();

            $this->addCustomFlash('success', 'Signalement', 'Le message a été signalé, merci !');

            return $this->redirectToRoute('thread.show', [
                'slug' => $message->getThread()->getSlug()
            ]);
        } else {
            throw new Exception("Jeton CSRF invalide !");
        }
    }
}

<?php

namespace App\Controller;


use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\ReportRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends BaseController
{
    /**
     * @Route("/forums/messages/{id}/edit", name="message.edit")
     * @Security("is_granted('ROLE_USER') and user === message.getAuthor()", message="Vous ne pouvez pas éditer le message d'un autre utilisateur !")
     * @param Message $message
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function edit(Message $message, Request $request): Response
    {
        $thread = $message->getThread();

        $route = $this->redirectToRoute('thread.show', [
            'id' => $thread->getId(),
            'slug' => $thread->getSlug(),
            '_fragment' => $message->getId()
        ]);

        if ($thread->getLocked() === null) {
            $form = $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                $this->addCustomFlash('success', 'Message', 'Votre message a bien été modifié !');
                return $route;
            }

            return $this->render('message/edit.html.twig', [
                'message' => $message,
                'form' => $form->createView(),
            ]);
        }

        $this->addCustomFlash('error', 'Message', 'Vous ne pouvez pas modifier votre message, le sujet est verrouillé !');
        return $route;
    }

    /**
     * @Route("/forums/messages/{id}/delete", name="message.delete")
     * @IsGranted("ROLE_MODERATOR")
     * @param Message $message
     * @param ObjectManager $manager
     * @param MessageRepository $messageRepository
     * @param ReportRepository $reportRepository
     * @return Response
     */
    public function delete(Message $message, ObjectManager $manager, MessageRepository $messageRepository, ReportRepository $reportRepository): Response
    {
        // TODO Add custom flash if message doesn't exists

        $thread = $message->getThread();
        $reports = $reportRepository->findByMessage($message);

        foreach ($reports as $report) {
            $manager->remove($report);
        }

        $manager->remove($message);
        $manager->flush();

        $lastMessage = $messageRepository->findLastMessageByThread($thread);

        if (!$lastMessage) {
            $manager->remove($thread);
            $manager->flush();

            $this->addCustomFlash('success', 'Message', 'Le message ainsi que le thread ont été supprimé !');

            return $this->redirectToRoute('forum.show', [
                'id' => $thread->getForum()->getId(),
                'slug' => $thread->getForum()->getSlug()
            ]);
        }

        $this->addCustomFlash('success', 'Message', 'Le message a été supprimé !');

        return $this->redirectToRoute('thread.show', [
            'id' => $thread->getId(),
            'slug' => $thread->getSlug(),
            '_fragment' => $lastMessage->getId()
        ]);
    }
}
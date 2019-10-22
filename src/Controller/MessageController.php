<?php

namespace App\Controller;


use App\Entity\Message;
use App\Form\MessageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
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

        $redirectionRoute = $this->redirectToRoute('thread.show', [
            'id' => $thread->getId(),
            'slug' => $thread->getSlug(),
            '_fragment' => $message->getId()
        ]);

        if ($thread->getLocked() === null) {
            $form = $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                return $redirectionRoute;
            }

            return $this->render('forums/message_edit.html.twig', [
                'message' => $message,
                'form' => $form->createView(),
            ]);
        }

        return $redirectionRoute;
    }
}
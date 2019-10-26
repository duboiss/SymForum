<?php

namespace App\Controller;


use App\Entity\Message;
use App\Form\MessageType;
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
}
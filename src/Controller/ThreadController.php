<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Thread;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThreadController extends BaseController
{
    /**
     * @Route("/forums/threads/{id}-{slug}", name="thread.show")
     * @param Thread $thread
     * @param MessageRepository $repo
     * @param Request $request
     * @return Response
     */
    public function show(Thread $thread, MessageRepository $repo, Request $request): Response
    {
        $messages = $repo->findMessagesByThreadWithAuthor($thread);

        $form = $this->createForm(MessageType::class, new Message(), [
            'action' => $this->generateUrl('message.add', ['id' => $thread->getId()])
        ]);

        return $this->render('thread/thread.html.twig', [
            'thread' => $thread,
            'messages' => $messages,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/forums/threads/{id}/lock", name="thread.lock")
     * @IsGranted("ROLE_MODERATOR")
     * @param Thread $thread
     * @param ObjectManager $manager
     * @return Response
     */
    public function lock(Thread $thread, ObjectManager $manager): Response
    {
        if ($thread->getLocked()) {
            $this->addCustomFlash('error', 'Sujet', 'Ce sujet est déjà fermé !');
        } else {
            $thread->setLocked(true);
            $manager->flush();

            $this->addCustomFlash('success', 'Sujet', 'Le sujet a été fermé !');
        }

        return $this->redirectToRoute('thread.show', [
            'id' => $thread->getId(),
            'slug' => $thread->getSlug()
        ]);
    }

    /**
     * @Route("/forums/threads/{id}/unlock", name="thread.unlock")
     * @IsGranted("ROLE_MODERATOR")
     * @param Thread $thread
     * @param ObjectManager $manager
     * @return Response
     */
    public function unlock(Thread $thread, ObjectManager $manager): Response
    {
        if (!$thread->getLocked()) {
            $this->addCustomFlash('error', 'Sujet', 'Ce sujet est déjà ouvert !');
        } else {
            $thread->setLocked(false);
            $manager->flush();

            $this->addCustomFlash('success', 'Sujet', 'Le sujet a été ouvert !');
        }

        return $this->redirectToRoute('thread.show', [
            'id' => $thread->getId(),
            'slug' => $thread->getSlug()
        ]);
    }

    /**
     * @Route("/forums/threads/{id}/delete", name="thread.delete")
     * @IsGranted("ROLE_MODERATOR")
     * @param Thread $thread
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Thread $thread, ObjectManager $manager): Response
    {
        $forum = $thread->getForum();

        $manager->remove($thread);
        $manager->flush();

        $this->addCustomFlash('success', 'Sujet', 'Le sujet a été supprimé !');

        return $this->redirectToRoute('forum.show', [
            'id' => $forum->getId(),
            'slug' => $forum->getSlug()
        ]);
    }
}

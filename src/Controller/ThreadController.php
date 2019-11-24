<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Thread;
use App\Form\MessageType;
use App\Form\ThreadType;
use App\Repository\MessageRepository;
use App\Service\AntispamService;
use App\Service\MessageService;
use App\Service\ThreadService;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThreadController extends BaseController
{
    /**
     * @Route("/forums/threads/{slug}", name="thread.show")
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
     * @Route("/forums/{slug}/new-thread", name="thread.new")
     * @param Forum $forum
     * @param Request $request
     * @param AntispamService $antispam
     * @param ThreadService $threadService
     * @param MessageService $messageService
     * @return Response
     */
    public function new(Forum $forum, Request $request, AntispamService $antispam, ThreadService $threadService, MessageService $messageService): Response
    {

        $form = $this->createForm(ThreadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$forum->getLocked()) {
                $user = $this->getUser();

                if (!$antispam->canPostThread($user)) {
                    $this->addCustomFlash('error', 'Sujet', 'Vous devez encore attendre un peu avant de pouvoir créer un sujet !');

                    return $this->redirectToRoute('forum.show', [
                        'slug' => $forum->getSlug()
                    ]);
                }

                $thread = $threadService->createThread($form['title']->getData(), $forum, $user);
                $message = $messageService->createMessage($form['message']->getData(), $thread, $user);

                $this->addCustomFlash('success', 'Sujet', 'Votre sujet a bien été crée !');

                return $this->redirectToRoute('thread.show', [
                    'slug' => $thread->getSlug(),
                    '_fragment' => $message->getId()
                ]);
            }

            $this->addCustomFlash('error', 'Sujet', 'Vous ne pouvez pas ajouter de sujet, le forum est verrouillé !');

            return $this->redirectToRoute('forum.show', [
                'slug' => $forum->getSlug()
            ]);
        }

        return $this->render('thread/new.html.twig', [
            'forum' => $forum,
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
            'slug' => $thread->getSlug()
        ]);
    }

    /**
     * @Route("/forums/threads/{id}/delete", name="thread.delete")
     * @IsGranted("ROLE_MODERATOR")
     * @param Thread $thread
     * @param ObjectManager $manager
     * @param MessageRepository $messageRepository
     * @return Response
     */
    public function delete(Thread $thread, ObjectManager $manager, MessageRepository $messageRepository): Response
    {
        // TODO Add custom flash if thread doesn't exists

        $forum = $thread->getForum();
        $lastMessage = $thread->getLastMessage();

        if ($forum->getLastMessage() === $lastMessage) {
            $forum->setLastMessage(null);
        }

        $thread->setLastMessage(null);
        $manager->remove($lastMessage);

        foreach ($thread->getMessages() as $message) {
            $manager->remove($message);
        }

        $manager->flush();

        $manager->remove($thread);
        $manager->flush();

        if (!$forum->getLastMessage()) {
            $forum->setLastMessage($messageRepository->findLastMessageByForum($forum));
            $manager->flush();
        }

        $this->addCustomFlash('success', 'Sujet', 'Le sujet a été supprimé !');

        return $this->redirectToRoute('forum.show', [
            'slug' => $forum->getSlug()
        ]);
    }
}

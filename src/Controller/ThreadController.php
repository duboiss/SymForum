<?php

namespace App\Controller;

use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use App\Form\MessageType;
use App\Form\ThreadType;
use App\Repository\MessageRepository;
use App\Service\MessageService;
use App\Service\OptionService;
use App\Service\ThreadService;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThreadController extends BaseController
{
    /**
     * @Route("/forums/threads/{slug}", name="thread.show", methods={"GET", "POST"})
     * @param Thread $thread
     * @param MessageRepository $messageRepository
     * @param Request $request
     * @param MessageService $messageService
     * @param PaginatorInterface $paginator
     * @param OptionService $optionService
     * @return Response
     */
    public function show(Thread $thread, MessageRepository $messageRepository, Request $request, MessageService $messageService, PaginatorInterface $paginator, OptionService $optionService): Response
    {
        $form = $this->createForm(MessageType::class, null, [
            'action' => $request->getUri() . '#message',
            'attr' => ['id' => 'message']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            if (!$messageService->canPostMessage($thread, $user)) {
                /** @var Message $lastMessage */
                $lastMessage = $thread->getLastMessage();

                return $this->redirectToRoute('message.show', [
                    'id' => $lastMessage->getId()
                ]);
            }

            $message = $messageService->createMessage($form['content']->getData(), $thread);

            $this->addCustomFlash('success', 'Message', 'Votre message a bien été posté !');

            return $this->redirectToRoute('message.show', [
                'id' => $message->getId()
            ]);
        }

        $messages = $messageRepository->findMessagesByThreadWithAuthorQb($thread);

        $pagination = $paginator->paginate(
            $messages,
            $request->query->getInt('page', 1),
            (int) $optionService->get('messages_per_thread', '10'),
        );

        return $this->render('thread/show.html.twig', [
            'thread' => $thread,
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/forums/{slug}/new-thread", name="thread.new", methods={"GET", "POST"})
     * @IsGranted("ROLE_USER")
     * @param Forum $forum
     * @param Request $request
     * @param ThreadService $threadService
     * @param MessageService $messageService
     * @return Response
     */
    public function new(Forum $forum, Request $request, ThreadService $threadService, MessageService $messageService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$threadService->canPostThread($forum, $user)) {
            return $this->redirectToRoute('forum.show', [
                'slug' => $forum->getSlug()
            ]);
        }

        $form = $this->createForm(ThreadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lock = (bool) $request->request->get('lock');
            $pin = (bool) $request->request->get('pin');

            $thread = $threadService->createThread($form['title']->getData(), $forum, $lock, $pin);

            $messageService->createMessage($form['message']->getData(), $thread);

            $this->addCustomFlash('success', 'Sujet', 'Votre sujet a bien été crée !');

            return $this->redirectToRoute('thread.show', [
                'slug' => $thread->getSlug()
            ]);
        }

        return $this->render('thread/new.html.twig', [
            'forum' => $forum,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/forums/threads/{id}/delete", name="thread.delete", methods={"POST"})
     * @IsGranted("DELETE", subject="thread")
     * @param Thread $thread
     * @param ThreadService $threadService
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function delete(Thread $thread, Request $request, ThreadService $threadService): Response
    {
        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-thread', $submittedToken)) {
            $forum = $thread->getForum();
            $threadService->deleteThread($thread);

            $this->addCustomFlash('success', 'Sujet', 'Le sujet a été supprimé !');

            return $this->redirectToRoute('forum.show', [
                'slug' => $forum->getSlug()
            ]);
        }
        throw new Exception('Jeton CSRF invalide !');
    }

    /**
     * @Route("/forums/threads/{id}/lock", name="thread.lock", methods={"GET"})
     * @IsGranted("LOCK", subject="thread")
     * @param Thread $thread
     * @param ThreadService $threadService
     * @param Request $request
     * @return Response
     */
    public function lock(Thread $thread, ThreadService $threadService, Request $request): Response
    {
        $threadService->lock($thread);
        $this->addCustomFlash('success', 'Sujet', 'Le sujet a été fermé !');

        return $this->redirectToReferer($request);
    }

    /**
     * @Route("/forums/threads/{id}/unlock", name="thread.unlock", methods={"GET"})
     * @IsGranted("LOCK", subject="thread")
     * @param Thread $thread
     * @param ThreadService $threadService
     * @param Request $request
     * @return Response
     */
    public function unlock(Thread $thread, ThreadService $threadService, Request $request): Response
    {
        $threadService->unlock($thread);
        $this->addCustomFlash('success', 'Sujet', 'Le sujet a été ouvert !');

        return $this->redirectToReferer($request);
    }

    /**
     * @Route("/forums/threads/{id}/pin", name="thread.pin", methods={"GET"})
     * @IsGranted("PIN", subject="thread")
     * @param Thread $thread
     * @param ThreadService $threadService
     * @param Request $request
     * @return Response
     */
    public function pin(Thread $thread, ThreadService $threadService, Request $request): Response
    {
        $threadService->pin($thread);
        $this->addCustomFlash('success', 'Sujet', 'Le sujet a été épinglé !');

        return $this->redirectToReferer($request);
    }

    /**
     * @Route("/forums/threads/{id}/unpin", name="thread.unpin", methods={"GET"})
     * @IsGranted("PIN", subject="thread")
     * @param Thread $thread
     * @param ThreadService $threadService
     * @param Request $request
     * @return Response
     */
    public function unpin(Thread $thread, ThreadService $threadService, Request $request): Response
    {
        $threadService->unpin($thread);
        $this->addCustomFlash('success', 'Sujet', 'Le sujet a été détaché !');

        return $this->redirectToReferer($request);
    }
}

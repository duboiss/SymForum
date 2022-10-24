<?php

declare(strict_types=1);

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
use Knp\Component\Pager\PaginatorInterface;
use RuntimeException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/forums', name: 'thread.')]
class ThreadController extends AbstractBaseController
{
    public function __construct(private readonly RequestStack $requestStack, private readonly DecoderInterface $decoder, private readonly TranslatorInterface $translator)
    {
        parent::__construct($requestStack, $this->decoder);
    }

    #[Route(path: '/threads/{slug}', name: 'show', methods: ['GET', 'POST'])]
    public function show(Thread $thread, MessageRepository $messageRepository, Request $request, MessageService $messageService, PaginatorInterface $paginator, OptionService $optionService): Response
    {
        $form = $this->createForm(MessageType::class, null, [
            'action' => $request->getUri() . '#message',
            'attr' => ['uuid' => 'message'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();

            if (!$messageService->canPostMessage($thread, $user)) {
                /** @var Message $lastMessage */
                $lastMessage = $thread->getLastMessage();

                return $this->redirectToRoute('message.show', [
                    'uuid' => $lastMessage->getUuid(),
                ]);
            }

            if (!$form['content']) {
                throw new RuntimeException($this->translator->trans('The request is not complete'));
            }
            $message = $messageService->createMessage($form['content']->getData(), $thread);

            $this->addCustomFlash('success', $this->translator->trans('Message'), $this->translator->trans('Your message has been posted'));

            return $this->redirectToRoute('message.show', [
                'uuid' => $message->getUuidBase32(),
            ]);
        }

        $pagination = $paginator->paginate(
            $messageRepository->findMessagesByThreadWithAuthorAndLikesQb($thread),
            $request->query->getInt('page', 1),
            (int) $optionService->get('messages_per_thread', '10'),
        );

        return $this->renderForm('thread/show.html.twig', [
            'thread' => $thread,
            'pagination' => $pagination,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/{slug}/new-thread', name: 'new', methods: ['GET', 'POST'])]
    public function new(Forum $forum, Request $request, ThreadService $threadService, MessageService $messageService): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$threadService->canPostThread($forum, $user)) {
            return $this->redirectToRoute('forum.show', [
                'slug' => $forum->getSlug(),
            ]);
        }

        $form = $this->createForm(ThreadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $lock = (bool) $request->request->get('lock');
            $pin = (bool) $request->request->get('pin');
            if (!$form['title'] || !$form['message']) {
                throw new \RuntimeException($this->translator->trans('The request is not complete'));
            }

            $thread = $threadService->createThread($form['title']->getData(), $forum, $lock, $pin);

            $messageService->createMessage($form['message']->getData(), $thread);

            $this->addCustomFlash('success', $this->translator->trans('Thread'), $this->translator->trans('Your thread has been created'));

            return $this->redirectToRoute('thread.show', [
                'slug' => $thread->getSlug(),
            ]);
        }

        return $this->renderForm('thread/new.html.twig', [
            'forum' => $forum,
            'form' => $form,
        ]);
    }

    #[IsGranted('DELETE', subject: 'thread')]
    #[Route(path: '/threads/{slug}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Thread $thread, Request $request, ThreadService $threadService): Response
    {
        $submittedToken = (string) $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-thread', $submittedToken)) {
            $forum = $thread->getForum();
            $threadService->deleteThread($thread);

            $this->addCustomFlash('success', $this->translator->trans('Thread'), $this->translator->trans('The thread has been deleted'));

            return $this->redirectToRoute('forum.show', [
                'slug' => $forum?->getSlug(),
            ]);
        }

        throw new RuntimeException($this->translator->trans('Invalid CSRF token'));
    }

    #[IsGranted('LOCK', subject: 'thread')]
    #[Route(path: '/threads/{slug}/lock', name: 'lock', methods: ['GET'])]
    public function lock(Thread $thread, ThreadService $threadService, Request $request): Response
    {
        $threadService->lock($thread);
        $this->addCustomFlash('success', $this->translator->trans('Thread'), $this->translator->trans('The thread has been locked'));

        return $this->redirectToReferer($request);
    }

    #[IsGranted('LOCK', subject: 'thread')]
    #[Route(path: '/threads/{slug}/unlock', name: 'unlock', methods: ['GET'])]
    public function unlock(Thread $thread, ThreadService $threadService, Request $request): Response
    {
        $threadService->unlock($thread);
        $this->addCustomFlash('success', $this->translator->trans('Thread'), $this->translator->trans('The thread has been unlocked'));

        return $this->redirectToReferer($request);
    }

    #[IsGranted('PIN', subject: 'thread')]
    #[Route(path: '/threads/{slug}/pin', name: 'pin', methods: ['GET'])]
    public function pin(Thread $thread, ThreadService $threadService, Request $request): Response
    {
        $threadService->pin($thread);
        $this->addCustomFlash('success', $this->translator->trans('Thread'), $this->translator->trans('The thread has been pinned'));

        return $this->redirectToReferer($request);
    }

    #[IsGranted('PIN', subject: 'thread')]
    #[Route(path: '/threads/{slug}/unpin', name: 'unpin', methods: ['GET'])]
    public function unpin(Thread $thread, ThreadService $threadService, Request $request): Response
    {
        $threadService->unpin($thread);
        $this->addCustomFlash('success', $this->translator->trans('Thread'), $this->translator->trans('The thread has been unpinned'));

        return $this->redirectToReferer($request);
    }
}

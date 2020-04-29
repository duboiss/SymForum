<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/")
 */
class UserController extends BaseController
{
    /**
     * @Route("{slug}", name="user.profile", methods={"GET"})
     * @param User $user
     * @param ThreadRepository $threadRepository
     * @param MessageRepository $messageRepository
     * @return Response
     */
    public function profile(User $user, ThreadRepository $threadRepository, MessageRepository $messageRepository): Response
    {
        $lastThreads = $threadRepository->findLastThreadsByUser($user, 5);
        $lastMessages = $messageRepository->findLastMessagesByUser($user, 5);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'lastThreads' => $lastThreads,
            'lastMessages' => $lastMessages
        ]);
    }

    /**
     * @Route("{slug}/threads", name="user.threads")
     * @param User $user
     * @param ThreadRepository $threadRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function threads(User $user, ThreadRepository $threadRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $threadRepository->findThreadsByUserQb($user),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('user/threads.html.twig', [
            'user' => $user,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("{slug}/messages", name="user.messages")
     * @param User $user
     * @param MessageRepository $messageRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function messages(User $user, MessageRepository $messageRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $messageRepository->findMessagesByUserQb($user),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('user/messages.html.twig', [
            'user' => $user,
            'pagination' => $pagination
        ]);
    }
}

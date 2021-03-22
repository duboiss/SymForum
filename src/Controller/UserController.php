<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/user', name: 'user.')]
class UserController extends AbstractBaseController
{
    #[Route(path: '/{slug}', name: 'profile', methods: ['GET'])]
    public function profile(User $user, ThreadRepository $threadRepository, MessageRepository $messageRepository): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'lastThreads' => $threadRepository->findLastThreadsByUser($user, 5),
            'lastMessages' => $messageRepository->findLastMessagesByUser($user, 5),
        ]);
    }

    #[Route(path: '/{slug}/threads', name: 'threads', methods: ['GET'])]
    public function threads(User $user, ThreadRepository $threadRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $threadRepository->findThreadsByUserQb($user),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('user/threads.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/{slug}/messages', name: 'messages', methods: ['GET'])]
    public function messages(User $user, MessageRepository $messageRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $messageRepository->findMessagesByUserQb($user),
            $request->query->getInt('page', 1),
            25
        );

        return $this->render('user/messages.html.twig', [
            'user' => $user,
            'pagination' => $pagination,
        ]);
    }
}

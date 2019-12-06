<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /**
     * @Route("/user/{slug}", name="user.profile")
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
}

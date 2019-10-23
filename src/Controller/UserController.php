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
     * @param ThreadRepository $threadsRepo
     * @param MessageRepository $messagesRepo
     * @return Response
     */
    public function profile(User $user, ThreadRepository $threadsRepo, MessageRepository $messagesRepo): Response
    {
        $lastThreads = $threadsRepo->findLastThreadsByUser($user, 5);
        $lastMessages = $messagesRepo->findLastMessagesByUser($user, 5);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'lastThreads' => $lastThreads,
            'lastMessages' => $lastMessages
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\ThreadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    /**
     * @Route("/login", name="user.login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="user.logout", methods={"GET"})
     */
    public function logout()
    {}

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

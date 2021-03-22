<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractBaseController
{
    #[Route(path: '/login', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('forum.index');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'target_url' => $request->query->get('redirect'),
        ]);
    }

    #[Route(path: '/logout', name: 'security.logout', methods: ['GET'])]
    public function logout(): void
    {
    }
}

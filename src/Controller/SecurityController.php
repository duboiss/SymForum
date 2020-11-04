<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractBaseController
{
    /**
     * @Route("/login", name="security.login", methods={"GET", "POST"})
     */
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

    /**
     * @Route("/logout", name="security.logout", methods="GET")
     */
    public function logout(): void
    {
    }

    /**
     * @Route("/logged-out", name="security.logged.out", methods="GET")
     */
    public function loggedOut(): Response
    {
        $this->addCustomFlash('success', 'Déconnexion', 'Vous êtes désormais déconnecté !');

        return $this->redirectToRoute('forum.index');
    }
}

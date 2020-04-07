<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends BaseController
{
    /**
     * @Route("/login", name="security.login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('forum.index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="security.logout", methods={"GET"})
     */
    public function logout(): void {}

    /**
     * @Route("/logged-out", name="security.logged.out")
     * @return Response
     */
    public function logged_out(): Response
    {
        $this->addCustomFlash('success', 'Déconnexion', 'Vous êtes désormais déconnecté !');
        return $this->redirectToRoute('forum.index');
    }
}

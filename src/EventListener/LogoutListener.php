<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LogoutListener
{
    private FlashBagInterface $flashBag;

    public function __construct(SessionInterface $session)
    {
        $this->flashBag = $session->getFlashBag();
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(): void
    {
        $this->flashBag->add('success', ['title' => 'Déconnexion', 'content' => 'Vous êtes désormais déconnecté !']);
    }
}

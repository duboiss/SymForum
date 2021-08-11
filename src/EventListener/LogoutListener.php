<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class LogoutListener
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(): void
    {
        $this->flashBag->add('success', ['title' => 'Déconnexion', 'content' => 'Vous êtes désormais déconnecté !']);
    }
}

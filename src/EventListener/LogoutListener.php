<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: LogoutEvent::class, dispatcher: 'security.event_dispatcher.main')]
class LogoutListener
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack, private TranslatorInterface $translator)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function onSymfonyComponentSecurityHttpEventLogoutEvent(): void
    {
        $this->flashBag->add('success', ['title' => $this->translator->trans('Logout'), 'content' => $this->translator->trans('You have logged out')]);
    }
}

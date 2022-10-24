<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\ValueObject\Locales;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Contracts\Translation\TranslatorInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    private readonly FlashBagInterface $flashBag;

    public function __construct(private readonly RequestStack $requestStack, private readonly TranslatorInterface $translator, private readonly LocaleSwitcher $localeSwitcher)
    {
        $this->flashBag = $requestStack->getSession()->getFlashBag();
    }

    public function notifyUserLogin(): void
    {
        $this->localeSwitcher->runWithLocale($this->requestStack->getSession()->get('_locale', Locales::DEFAULT), function (): void {
            $this->flashBag->add('info', [
                'title' => $this->translator->trans('Login'),
                'content' => $this->translator->trans('You have logged in'),
            ]);
        });
    }

    public function setUserLocale(InteractiveLoginEvent $event): void
    {
        /** @var User|null $user */
        $user = $event->getAuthenticationToken()->getUser();

        if ($user && $user->getLocale()) {
            $this->requestStack->getSession()->set('_locale', $user->getLocale());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => [
                ['notifyUserLogin', 10],
                ['setUserLocale', 30],
            ],
        ];
    }
}

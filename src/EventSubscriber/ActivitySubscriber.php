<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\OptionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ActivitySubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    private OptionService $optionService;

    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em, Security $security, OptionService $optionService, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->security = $security;
        $this->optionService = $optionService;
        $this->userRepository = $userRepository;
    }

    public function onTerminate(): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if ($user) {
            $user->setLastActivityAt(new DateTime());
            $this->em->flush();

            $maxOnlineUsers = (int) $this->optionService->get('max_online_users', '0');
            $nbOnlineUsers = $this->userRepository->countOnlineUsers();

            if ($nbOnlineUsers > $maxOnlineUsers) {
                $currentDate = date('d-m-Y à H:i:s');
                $this->optionService->set('max_online_users', (string) $nbOnlineUsers);
                $this->optionService->set('max_online_users_date', $currentDate);
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [['onTerminate', 20]],
        ];
    }
}

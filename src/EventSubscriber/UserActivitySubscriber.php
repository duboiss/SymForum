<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\OptionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class UserActivitySubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly Security $security, private readonly OptionService $optionService, private readonly UserRepository $userRepository)
    {
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
                $this->optionService->set('max_online_users', (string) $nbOnlineUsers);
                $this->optionService->set('max_online_users_date', date('d-m-Y à H:i:s'));
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

<?php

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\OptionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ActivityListener implements EventSubscriberInterface
{

    /** @var EntityManagerInterface */
    private $em;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var OptionService */
    private $optionService;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, OptionService $optionService, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->optionService = $optionService;
        $this->userRepository = $userRepository;
    }

    public function onTerminate()
    {
        if ($this->tokenStorage->getToken()) {
            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user instanceof UserInterface) {
                $user->setLastActivityAt(new DateTime());
                $this->em->flush();

                $maxOnlineUsers = (int) $this->optionService->get("max_online_users", "0");
                $nbOnlineUsers = $this->userRepository->countOnlineUsers();

                if ($nbOnlineUsers > $maxOnlineUsers) {
                    $currentDate = date("d-m-Y à H:i:s");
                    $this->optionService->set("max_online_users", (string) $nbOnlineUsers);
                    $this->optionService->set("max_online_users_date", $currentDate);
                }
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => [['onTerminate', 20]],
        ];
    }

}
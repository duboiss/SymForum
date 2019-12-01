<?php

namespace App\EventListener;

use App\Repository\UserRepository;
use App\Service\OptionService;
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
    private $repo;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, OptionService $optionService, UserRepository $repo)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->optionService = $optionService;
        $this->repo = $repo;
    }

    public function onTerminate()
    {
        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();

            if ($user instanceof UserInterface) {
                $user->setLastActivityAt(new \DateTime());
                $this->em->flush();

                $maxOnlineUsers = (int)$this->optionService->get("max_online_users", "0");
                $nbOnlineUsers = $this->repo->countOnlineUsers();

                if ($nbOnlineUsers > $maxOnlineUsers) {
                    $currentDate = date("d-m-Y à H:i:s");
                    $this->optionService->set("max_online_users", $nbOnlineUsers);
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
<?php

namespace App\Service;

use App\Entity\Message;
use App\Entity\MessageLike;
use App\Entity\User;
use App\Repository\MessageLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class MessageLikeService
{
    private Security $security;

    private EntityManagerInterface $em;

    private MessageLikeRepository $likeRepository;

    public function __construct(Security $security, EntityManagerInterface $em, MessageLikeRepository $likeRepository)
    {
        $this->security = $security;
        $this->em = $em;
        $this->likeRepository = $likeRepository;
    }

    public function likeMessage(Message $message): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($like = $this->likeRepository->findMessageLikeByUser($message, $user)) {
            $this->em->remove($like);
        } else {
            $messageLike = (new MessageLike())
                ->setMessage($message)
                ->setUser($user);

            $this->em->persist($messageLike);
        }

        $this->em->flush();
    }
}

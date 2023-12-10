<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Message;
use App\Entity\MessageLike;
use App\Entity\User;
use App\Repository\MessageLikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class MessageLikeService
{
    public function __construct(private readonly Security $security, private readonly EntityManagerInterface $em, private readonly MessageLikeRepository $likeRepository) {}

    public function likeMessage(Message $message): void
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($like = $this->likeRepository->findMessageLikeByUser($message, $user)) {
            $this->em->remove($like);
        } else {
            $messageLike = (new MessageLike())
                ->setMessage($message)
                ->setUser($user)
            ;

            $this->em->persist($messageLike);
        }

        $this->em->flush();
    }
}

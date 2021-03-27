<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageLikeRepository;
use App\Service\MessageLikeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/forums/messages', name: 'message.')]
class MessageLikeController extends AbstractBaseController
{
    #[IsGranted('LIKE', subject: 'message')]
    #[Route(path: '/{id}/like', name: 'like', methods: ['POST'])]
    public function like(Message $message, MessageLikeService $likeService, MessageLikeRepository $likeRepository): Response
    {
        $likeService->likeMessage($message);

        return $this->json($likeRepository->count(['message' => $message]));
    }
}

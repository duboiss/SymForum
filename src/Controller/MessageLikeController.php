<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\MessageLikeRepository;
use App\Service\MessageLikeService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/forums")
 */
class MessageLikeController extends BaseController
{
    /**
     * @Route("/messages/{id}/like", name="message.like", methods="POST")
     * @IsGranted("LIKE", subject="message")
     */
    public function like(Message $message, MessageLikeService $likeService, MessageLikeRepository $likeRepository): Response
    {
        $likeService->likeMessage($message);

        return $this->json($likeRepository->count(['message' => $message]));
    }
}

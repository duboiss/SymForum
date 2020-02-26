<?php

namespace App\Twig;

use App\Entity\Message;
use App\Service\ThreadService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ThreadExtension extends AbstractExtension
{
    /* @var ThreadService */
    private $threadService;

    public function __construct(ThreadService $threadService)
    {
        $this->threadService = $threadService;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('message_link', [$this, 'getMessageLink']),
        ];
    }

    /**
     * @param Message $message
     * @return string
     */
    public function getMessageLink(Message $message): string
    {
        return $this->threadService->getMessageLink($message);
    }
}
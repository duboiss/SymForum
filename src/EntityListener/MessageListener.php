<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Message;
use App\Service\MeiliSearchService;
use App\Trait\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;

class MessageListener
{
    use EntityManagerTrait;

    private const MEILI_INDEX = 'messages';

    public function __construct(private MeiliSearchService $meiliSearchService, private EntityManagerInterface $em)
    {
    }

    public function postPersist(Message $message): void
    {
        $documents = [[
            'id' => $message->getId(),
            'author' => $message->getAuthor() ? $message->getAuthor()->getPseudo() : null,
            'content' => $message->getContent(),
        ]];

        $this->meiliSearchService->addDocuments(self::MEILI_INDEX, $documents, 'id');
    }

    public function postUpdate(Message $message): void
    {
        $changes = $this->getChangeSet($this->em, $message);

        if (!array_key_exists('author', $changes) || !array_key_exists('content', $changes)) {
            return;
        }

        $documents = [[
            'id' => $message->getId(),
            'author' => $message->getAuthor() ? $message->getAuthor()->getPseudo() : null,
            'content' => $message->getContent(),
        ]];

        $this->meiliSearchService->updateDocuments(self::MEILI_INDEX, $documents, 'id');
    }

    public function preRemove(Message $message): void
    {
        $this->meiliSearchService->deleteDocument(self::MEILI_INDEX, $message->getId());
    }
}

<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Thread;
use App\Service\MeiliSearchService;
use App\Trait\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;

class ThreadListener
{
    use EntityManagerTrait;

    private const MEILI_INDEX = 'threads';

    public function __construct(private MeiliSearchService $meiliSearchService, private EntityManagerInterface $em)
    {
    }

    public function postPersist(Thread $thread): void
    {
        $documents = [[
            'id' => $thread->getId(),
            'title' => $thread->getTitle() ,
            'author' => $thread->getAuthor() ? $thread->getAuthor()->getPseudo() : null,
        ]];

        $this->meiliSearchService->addDocuments(self::MEILI_INDEX, $documents, 'id');
    }

    public function postUpdate(Thread $thread): void
    {
        $changes = $this->getChangeSet($this->em, $thread);

        if (!array_key_exists('title', $changes) || !array_key_exists('author', $changes)) {
            return;
        }

        $documents = [[
            'id' => $thread->getId(),
            'title' => $thread->getTitle() ,
            'author' => $thread->getAuthor() ? $thread->getAuthor()->getPseudo() : null,
        ]];

        $this->meiliSearchService->updateDocuments(self::MEILI_INDEX, $documents, 'id');
    }

    public function preRemove(Thread $thread): void
    {
        $this->meiliSearchService->deleteDocument(self::MEILI_INDEX, $thread->getId());
    }
}

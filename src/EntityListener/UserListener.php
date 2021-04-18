<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\User;
use App\Service\MeiliSearchService;
use App\Trait\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;

class UserListener
{
    use EntityManagerTrait;

    private const MEILI_INDEX = 'users';

    public function __construct(private MeiliSearchService $meiliSearchService, private EntityManagerInterface $em)
    {
    }

    public function postPersist(User $user): void
    {
        $documents = [[
            'id' => $user->getId(),
            'pseudo' => $user->getPseudo(),
            'lastActivityAt' => $user->getLastActivityAt() ? $user->getLastActivityAt()->format(\DateTimeInterface::ATOM) : null,
        ]];

        $this->meiliSearchService->addDocuments(self::MEILI_INDEX, $documents, 'id');
    }

    public function postUpdate(User $user): void
    {
        $changes = $this->getChangeSet($this->em, $user);

        if (!array_key_exists('pseudo', $changes) || !array_key_exists('lastActivityAt', $changes)) {
            return;
        }

        $documents = [[
            'id' => $user->getId(),
            'pseudo' => $user->getPseudo(),
            'lastActivityAt' => $user->getLastActivityAt() ? $user->getLastActivityAt()->format(\DateTimeInterface::ATOM) : null,
        ]];

        $this->meiliSearchService->updateDocuments(self::MEILI_INDEX, $documents, 'id');
    }

    public function preRemove(User $user): void
    {
        $this->meiliSearchService->deleteDocument(self::MEILI_INDEX, $user->getId());
    }
}

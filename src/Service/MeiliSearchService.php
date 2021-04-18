<?php

declare(strict_types=1);

namespace App\Service;

use MeiliSearch\Client;
use MeiliSearch\Endpoints\Indexes;
use MeiliSearch\Exceptions\ApiException;

class MeiliSearchService
{
    private ?Client $client;

    public function __construct($meiliUrl, $meiliMasterKey)
    {
        $this->client = new Client($meiliUrl, $meiliMasterKey);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getIndex(string $index): Indexes|null
    {
        try {
            return $this->client->getOrCreateIndex($index);
        } catch (ApiException) {
        }

        return null;
    }

    public function addDocuments(string $index, array $documents, ?string $primaryKey = null): void
    {
        $indexResolved = $this->getIndex($index);

        if ($indexResolved instanceof Indexes) {
            $indexResolved->addDocuments($documents, $primaryKey);
        }
    }

    public function updateDocuments(string $index, array $documents, ?string $primaryKey = null): void
    {
        $indexResolved = $this->getIndex($index);

        if ($indexResolved instanceof Indexes) {
            $indexResolved->updateDocuments($documents, $primaryKey);
        }
    }

    public function deleteDocument(string $index, string|int $documentId): void
    {
        $indexResolved = $this->getIndex($index);

        if ($indexResolved instanceof Indexes) {
            $indexResolved->deleteDocument($documentId);
        }
    }

    public function deleteAllIndexes()
    {
        $this->client->deleteAllIndexes();
    }
}

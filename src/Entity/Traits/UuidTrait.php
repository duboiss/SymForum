<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait UuidTrait
{
    #[ORM\Column(type: 'uuid', unique: true, nullable: false)]
    private ?Uuid $uuid = null;

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function getUuidBase32(): ?string
    {
        return $this->uuid?->toBase32();
    }
}

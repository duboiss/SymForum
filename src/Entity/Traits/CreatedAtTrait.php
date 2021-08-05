<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait CreatedAtTrait
{
    /**
     * @Gedmo\Timestampable(on="create")
     */
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $createdAt;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}

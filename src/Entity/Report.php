<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\PrimaryKeyTrait;
use App\Entity\Traits\UuidTrait;
use App\Repository\ReportRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    use CreatedAtTrait;
    use PrimaryKeyTrait;
    use UuidTrait;

    public const REASON_MIN_LENGTH = 8;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Message $message = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Vous devez indiquer un motif.')]
    #[Assert\Length(min: self::REASON_MIN_LENGTH, max: 255, minMessage: 'Votre message doit faire au moins 10 caractères.', maxMessage: 'Votre message doit faire au maximum 255 caractères.')]
    private ?string $reason = null;

    #[Gedmo\Blameable(on: 'create')]
    #[ORM\ManyToOne(inversedBy: 'reports')]
    private ?User $reportedBy = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $treatedAt = null;

    #[Gedmo\Blameable(on: 'change', field: ['treatedAt'])]
    #[ORM\ManyToOne(inversedBy: 'treatedReports')]
    private ?User $treatedBy = null;

    public function __construct()
    {
        $this->uuid = Uuid::v4();
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getReportedBy(): ?User
    {
        return $this->reportedBy;
    }

    public function setReportedBy(?User $reportedBy): self
    {
        $this->reportedBy = $reportedBy;

        return $this;
    }

    public function getTreatedAt(): ?DateTimeInterface
    {
        return $this->treatedAt;
    }

    public function setTreatedAt(?DateTimeInterface $treatedAt): self
    {
        $this->treatedAt = $treatedAt;

        return $this;
    }

    public function getTreatedBy(): ?User
    {
        return $this->treatedBy;
    }

    public function setTreatedBy(?User $treatedBy): self
    {
        $this->treatedBy = $treatedBy;

        return $this;
    }

    public function isTreated(): bool
    {
        return $this->treatedAt !== null;
    }
}

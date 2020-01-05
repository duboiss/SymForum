<?php

namespace App\Entity;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReportRepository")
 */
class Report
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="reports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez indiquer un motif.")
     * @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "Votre message doit faire au moins 10 caractères.",
     *      maxMessage = "Votre message doit faire au maximum 255 caractères."
     * )
     */
    private $reason;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $reportedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reports")
     */
    private $reportedBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $treatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="treatedReports")
     */
    private $treatedBy;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReportedAt(): ?DateTimeInterface
    {
        return $this->reportedAt;
    }

    public function setReportedAt(DateTimeInterface $reportedAt): self
    {
        $this->reportedAt = $reportedAt;

        return $this;
    }

    public function getReportedBy(): ?User
    {
        return $this->reportedBy;
    }

    public function setReportedBy($reportedBy): self
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

<?php

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use App\Repository\MessageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    use PrimaryKeyTrait;
    use TimestampableEntity;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="messages")
     * @Gedmo\Blameable(on="create")
     */
    private $author;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Votre message ne peut pas être vide.")
     * @Assert\Length(
     *      min = 10,
     *      max = 6000,
     *      minMessage = "Votre message doit faire au moins 3 caractères.",
     *      maxMessage = "Votre message doit faire au maximum {{ limit }} caractères."
     * )
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Thread::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $thread;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="updatedMessages")
     * @Gedmo\Blameable(on="change", field={"content"})
     */
    private $updatedBy;

    /**
     * @ORM\OneToMany(targetEntity=Report::class, mappedBy="message", orphanRemoval=true)
     */
    private $reports;

    public function __construct()
    {
        $this->reports = new ArrayCollection();
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getThread(): Thread
    {
        return $this->thread;
    }

    public function setThread(?Thread $thread): self
    {
        $this->thread = $thread;

        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setMessage($this);
        }

        return $this;
    }

    public function removeReport(Report $report): self
    {
        if ($this->reports->contains($report)) {
            $this->reports->removeElement($report);
            // set the owning side to null (unless already changed)
            if ($report->getMessage() === $this) {
                $report->setMessage(null);
            }
        }

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\PrimaryKeyTrait;
use App\Repository\ThreadRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ThreadRepository::class)]
#[UniqueEntity('slug')]
class Thread
{
    use CreatedAtTrait;
    use PrimaryKeyTrait;

    public const TITLE_MIN_LENGTH = 12;
    public const TITLE_MAX_LENGTH = 50;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    /**
     * @Gedmo\Slug(fields={"title"})
     */
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    /**
     * @Gedmo\Blameable(on="create")
     */
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'threads')]
    private ?User $author = null;

    #[ORM\ManyToOne(targetEntity: Forum::class, inversedBy: 'threads')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private $forum;

    #[ORM\Column]
    #[Assert\NotNull]
    private bool $isLock = false;

    #[ORM\Column]
    #[Assert\NotNull]
    private bool $isPin = false;

    #[ORM\OneToOne(targetEntity: Message::class, cascade: ['persist', 'remove'])]
    private ?Message $lastMessage = null;

    #[ORM\OneToMany(mappedBy: 'thread', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\Column(type: 'smallint')]
    private int $totalMessages = 0;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getForum(): Forum
    {
        return $this->forum;
    }

    public function setForum(?Forum $forum): self
    {
        $this->forum = $forum;

        return $this;
    }

    public function isLock(): bool
    {
        return $this->isLock;
    }

    public function setLock(bool $isLock): self
    {
        $this->isLock = $isLock;

        return $this;
    }

    public function isPin(): bool
    {
        return $this->isPin;
    }

    public function setPin(bool $isPin): self
    {
        $this->isPin = $isPin;

        return $this;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Message $lastMessage): self
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setThread($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getThread() === $this) {
                $message->setThread(null);
            }
        }

        return $this;
    }

    public function getTotalMessages(): ?int
    {
        return $this->totalMessages;
    }

    public function incrementTotalMessages(): self
    {
        ++$this->totalMessages;

        return $this;
    }

    public function decrementTotalMessages(): self
    {
        --$this->totalMessages;

        return $this;
    }

    public function getTotalAnswers(): ?int
    {
        return $this->totalMessages - 1;
    }
}

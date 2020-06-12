<?php

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

/**
 * @ORM\Entity(repositoryClass=ThreadRepository::class)
 * @UniqueEntity("slug")
 */
class Thread
{
    use PrimaryKeyTrait;
    use CreatedAtTrait;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"title"})
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="threads")
     * @Gedmo\Blameable(on="create")
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=Forum::class, inversedBy="threads")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $forum;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isLock = false;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isPin = false;

    /**
     * @ORM\OneToOne(targetEntity=Message::class, cascade={"persist", "remove"})
     */
    private $lastMessage;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="thread", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\Column(type="smallint")
     */
    private $totalMessages = 0;

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

    public function setIsLock(bool $isLock): self
    {
        $this->isLock = $isLock;

        return $this;
    }

    public function isPin(): bool
    {
        return $this->isPin;
    }

    public function setIsPin(bool $isPin): self
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
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
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
        $this->totalMessages++;

        return $this;
    }

    public function decrementTotalMessages(): self
    {
        $this->totalMessages--;

        return $this;
    }

    public function getTotalAnswers(): ?int
    {
        return $this->totalMessages - 1;
    }
}

<?php

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use App\Repository\ForumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ForumRepository::class)
 * @UniqueEntity("slug")
 */
class Forum
{
    use PrimaryKeyTrait;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="forums")
     * @ORM\JoinColumn(nullable=true)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Forum", inversedBy="forums")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Forum", mappedBy="parent")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $forums;

    /**
     * @ORM\Column(type="smallint")
     * @Assert\NotBlank()
     * @Assert\Positive(message="La position doit correspondre à un nombre positif.")
     */
    private $position;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $isLock = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Message", cascade={"persist", "remove"})
     */
    private $lastMessage;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Thread", mappedBy="forum", orphanRemoval=true)
     */
    private $threads;

    /**
     * @ORM\Column(type="smallint")
     */
    private $totalThreads = 0;

    /**
     * @ORM\Column(type="smallint")
     */
    private $totalMessages = 0;

    public function __construct()
    {
        $this->forums = new ArrayCollection();
        $this->threads = new ArrayCollection();
    }

    public function getRootCategory(): ?Category
    {
        if (null !== ($parent = $this->getParent())) {
            return $parent->getRootCategory();
        }

        return $this->getCategory();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getForums(): Collection
    {
        return $this->forums;
    }

    public function addForum(self $forum): self
    {
        if (!$this->forums->contains($forum)) {
            $this->forums[] = $forum;
            $forum->setParent($this);
        }

        return $this;
    }

    public function removeForum(self $forum): self
    {
        if ($this->forums->contains($forum)) {
            $this->forums->removeElement($forum);
            // set the owning side to null (unless already changed)
            if ($forum->getParent() === $this) {
                $forum->setParent(null);
            }
        }

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

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
     * @return Collection|Thread[]
     */
    public function getThreads(): Collection
    {
        return $this->threads;
    }

    public function getTotalThreads(): int
    {
        $totalThreads = $this->totalThreads;

        foreach ($this->forums as $forum) {
            $totalThreads += $forum->getTotalThreads();
        }

        return $totalThreads;
    }

    public function incrementTotalThreads(): self
    {
        ++$this->totalThreads;

        return $this;
    }

    public function decrementTotalThreads(): self
    {
        --$this->totalThreads;

        return $this;
    }

    public function getTotalMessages(): int
    {
        $totalMessages = $this->totalMessages;

        foreach ($this->forums as $forum) {
            $totalMessages += $forum->getTotalMessages();
        }

        return $totalMessages;
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

    public function addThread(Thread $thread): self
    {
        if (!$this->threads->contains($thread)) {
            $this->threads[] = $thread;
            $thread->setForum($this);
        }

        return $this;
    }

    public function removeThread(Thread $thread): self
    {
        if ($this->threads->contains($thread)) {
            $this->threads->removeElement($thread);
            // set the owning side to null (unless already changed)
            if ($thread->getForum() === $this) {
                $thread->setForum(null);
            }
        }

        return $this;
    }
}

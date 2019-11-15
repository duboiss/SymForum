<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ForumRepository")
 * @UniqueEntity("slug")
 */
class Forum
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="forums")
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
     */
    private $position;

    /**
     * @ORM\Column(type="boolean")
     */
    private $locked = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Thread", mappedBy="forum", orphanRemoval=true)
     */
    private $threads;

    public function __construct()
    {
        $this->forums = new ArrayCollection();
        $this->threads = new ArrayCollection();
    }

    public function getRootCategory(): Category
    {
        if (($parent = $this->getParent()) !== null) {
            return $parent->getRootCategory();
        }

        return $this->getCategory();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): self
    {
        $this->locked = $locked;

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
        $totalThreads = $this->threads->count();

        foreach ($this->forums as $forum) {
            $totalThreads += $forum->getTotalThreads();
        }

        return $totalThreads;
    }

    public function getTotalMessages(): int
    {
        $totalMessages = 0;

        foreach ($this->threads as $thread) {
            $totalMessages += $thread->getTotalMessages();
        }

        foreach ($this->forums as $forum) {
            foreach ($forum->threads as $thread) {
                $totalMessages += $thread->getTotalMessages();
            }
        }

        return $totalMessages;
    }

    public function getLastMessage(): ?Message
    {
        $date = new \DateTime();
        $date->setDate(1970, 1, 1);

        $lastMessage = null;

        foreach ($this->threads as $thread) {
            if($date < $thread->getLastMessage()->getPublishedAt()) {
                $lastMessage = $thread->getLastMessage();
                $date = $thread->getLastMessage()->getPublishedAt();
            }
        }

        foreach ($this->forums as $forum) {
            foreach ($forum->threads as $thread) {
                if($date < $thread->getLastMessage()->getPublishedAt()) {
                    $lastMessage = $thread->getLastMessage();
                    $date = $thread->getLastMessage()->getPublishedAt();
                }
            }
        }

        return $lastMessage;
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

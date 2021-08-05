<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Entity\Traits\PrimaryKeyTrait;
use App\Repository\UserRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('pseudo')]
#[UniqueEntity('email')]
#[UniqueEntity('slug')]
class User implements UserInterface, \Stringable
{
    use CreatedAtTrait;
    use PrimaryKeyTrait;

    public const PSEUDO_MIN_LENGTH = 3;
    public const PSEUDO_MAX_LENGTH = 10;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Regex(pattern: '^[a-z0-9]+$/i', message: 'Votre pseudo ne peut comporter que des lettres (a-z) ainsi que des chiffres.')]
    #[Assert\Length(min: self::PSEUDO_MIN_LENGTH, max: self::PSEUDO_MAX_LENGTH, minMessage: 'Votre pseudo doit faire au moins {{ limit }} caractères.', maxMessage: 'Votre pseudo doit faire au plus {{ limit }} caractères.')]
    private ?string $pseudo = null;

    /**
     * @Gedmo\Slug(fields={"pseudo"})
     */
    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $hash = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(message: 'Veuillez saisir une adresse email valide.')]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?DateTimeInterface $lastActivityAt = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Thread::class)]
    private Collection $threads;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'updatedBy', targetEntity: Message::class)]
    private Collection $updatedMessages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MessageLike::class, orphanRemoval: true)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'reportedBy', targetEntity: Report::class)]
    private Collection $reports;

    #[ORM\OneToMany(mappedBy: 'treatedBy', targetEntity: Report::class)]
    private Collection $treatedReports;

    public function __construct()
    {
        $this->threads = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->pseudo;
    }

    public function isActiveNow(): bool
    {
        $delay = new DateTime('5 minutes ago');

        return $this->getLastActivityAt() > $delay;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLastActivityAt(): ?DateTimeInterface
    {
        return $this->lastActivityAt;
    }

    public function setLastActivityAt(DateTimeInterface $lastActivityAt): self
    {
        $this->lastActivityAt = $lastActivityAt;

        return $this;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword()
    {
        return $this->hash;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
    }

    /**
     * @return Collection|Thread[]
     */
    public function getThreads(): Collection
    {
        return $this->threads;
    }

    public function addThread(Thread $thread): self
    {
        if (!$this->threads->contains($thread)) {
            $this->threads[] = $thread;
            $thread->setAuthor($this);
        }

        return $this;
    }

    public function removeThread(Thread $thread): self
    {
        if ($this->threads->removeElement($thread)) {
            // set the owning side to null (unless already changed)
            if ($thread->getAuthor() === $this) {
                $thread->setAuthor(null);
            }
        }

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
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[];
     */
    public function getUpdatedByMessages(): Collection
    {
        return $this->updatedMessages;
    }

    /**
     * @return Collection|MessageLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(MessageLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(MessageLike $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Report[]
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    /**
     * @return Collection|Report[]
     */
    public function getTreatedReports(): Collection
    {
        return $this->treatedReports;
    }
}

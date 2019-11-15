<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("pseudo")
 * @UniqueEntity("email")
 * @UniqueEntity("slug")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Regex(
     *     pattern="^[a-z0-9]+$/i",
     *     message="Votre pseudo ne peut comporter que des lettres (a-z) ainsi que des chiffres."
     * )
     * @Assert\Length(
     *      min = 3,
     *      max = 10,
     *      minMessage = "Votre pseudo doit faire au moins {{ limit }} caractères.",
     *      maxMessage = "Votre pseudo doit faire au plus {{ limit }} caractères."
     * )
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"pseudo"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message = "Veuillez saisir une adresse email valide.")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $registrationDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $registrationIp;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastActivityAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Thread", mappedBy="author")
     */
    private $threads;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="author")
     */
    private $messages;

    public function __construct()
    {
        $this->threads = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function isActiveNow(): bool
    {
        $delay = new DateTime('5 minutes ago');

        return ($this->getLastActivityAt() > $delay);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRegistrationDate(): ?DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getRegistrationIp(): ?string
    {
        return $this->registrationIp;
    }

    public function setRegistrationIp(string $registrationIp): self
    {
        $this->registrationIp = $registrationIp;

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

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles() {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword() {
        return $this->hash;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt() {}

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername() {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials() {}

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
        if ($this->threads->contains($thread)) {
            $this->threads->removeElement($thread);
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
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }
}

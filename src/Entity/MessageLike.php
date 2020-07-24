<?php

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use App\Repository\MessageLikeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageLikeRepository::class)
 */
class MessageLike
{
    use PrimaryKeyTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Message::class, inversedBy="likes")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Message $message;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="likes")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Thread;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ThreadVoter extends Voter
{
    private const LOCK = 'LOCK';
    private const PIN = 'PIN';
    private const DELETE = 'DELETE';

    public function __construct(private readonly Security $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::LOCK, self::PIN, self::DELETE], true)
            && $subject instanceof Thread;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::LOCK => $this->canLock(),
            self::PIN => $this->canPin(),
            self::DELETE => $this->canDelete(),
            default => false,
        };
    }

    private function canLock(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }

    private function canPin(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }

    private function canDelete(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }
}

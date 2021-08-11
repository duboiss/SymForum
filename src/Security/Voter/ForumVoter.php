<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Forum;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ForumVoter extends Voter
{
    private const LOCK = 'LOCK';

    public function __construct(private Security $security)
    {
    }

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::LOCK], true)
            && $subject instanceof Forum;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::LOCK => $this->canLock(),
            default => false,
        };
    }

    private function canLock(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }
}

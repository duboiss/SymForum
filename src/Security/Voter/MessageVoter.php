<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageVoter extends Voter
{
    private const EDIT = 'EDIT';
    private const DELETE = 'DELETE';
    private const LIKE = 'LIKE';
    private const REPORT = 'REPORT';

    public function __construct(private readonly Security $security) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::LIKE, self::REPORT], true)
            && $subject instanceof Message;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        $message = $subject;

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::EDIT => $this->canEdit($message, $user),
            self::DELETE => $this->canDelete(),
            self::LIKE => true,
            self::REPORT => $this->canReport($message, $user),
            default => false,
        };
    }

    private function canEdit(Message $message, User $user): bool
    {
        if ($this->security->isGranted('ROLE_MODERATOR')) {
            return true;
        }

        return $user === $message->getAuthor() && !$message->getThread()?->isLock();
    }

    private function canDelete(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }

    private function canReport(Message $message, User $user): bool
    {
        return $user !== $message->getAuthor();
    }
}

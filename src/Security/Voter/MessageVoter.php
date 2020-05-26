<?php

namespace App\Security\Voter;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageVoter extends Voter
{
    private const EDIT = 'EDIT';
    private const DELETE = 'DELETE';
    private const REPORT = 'REPORT';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::REPORT])
            && $subject instanceof Message;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        $message = $subject;

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($message, $user);
            case self::DELETE:
                return $this->canDelete();
            case self::REPORT:
                return $this->canReport($message, $user);
        }

        return false;
    }

    /**
     * @param Message $message
     * @param User $user
     * @return bool
     */
    private function canEdit(Message $message, User $user): bool
    {
        if($this->security->isGranted('ROLE_MODERATOR')) {
            return true;
        }
        return $user === $message->getAuthor() && !$message->getThread()->isLock();
    }

    /**
     * @return bool
     */
    private function canDelete(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }

    /**
     * @param Message $message
     * @param User $user
     * @return bool
     */
    private function canReport(Message $message, User $user): bool
    {
        return $user !== $message->getAuthor();
    }
}

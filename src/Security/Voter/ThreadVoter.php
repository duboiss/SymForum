<?php

namespace App\Security\Voter;

use App\Entity\Thread;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ThreadVoter extends Voter
{
    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['LOCK', 'PIN', 'DELETE'])
            && $subject instanceof Thread;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        $thread = $subject;

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'LOCK':
                return $this->canLock();
            case 'PIN':
                return $this->canPin();
            case 'DELETE':
                return $this->canDelete();
        }

        return false;
    }

    /**
     * @return bool
     */
    private function canLock(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }

    /**
     * @return bool
     */
    private function canPin(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }

    /**
     * @return bool
     */
    private function canDelete(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }
}

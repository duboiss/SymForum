<?php

namespace App\Security\Voter;

use App\Entity\Forum;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ForumVoter extends Voter
{
    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['LOCK'])
            && $subject instanceof Forum;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'LOCK':
                return $this->canLock();
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
}

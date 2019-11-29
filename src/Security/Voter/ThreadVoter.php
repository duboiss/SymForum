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
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['POST', 'DELETE'])
            && $subject instanceof Thread;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /**
         * @var User $user
         */
        $user = $token->getUser();

        $thread = $subject;

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'DELETE':
                return $this->canDelete();
        }

        return false;
    }


    /**
     * @return bool
     */
    private function canDelete(): bool
    {
        return $this->security->isGranted('ROLE_MODERATOR');
    }
}

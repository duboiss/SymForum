<?php

namespace App\Security\Voter;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MessageVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['EDIT'])
            && $subject instanceof Message;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /**
         * @var User $user
         */
        $user = $token->getUser();

        $message = $subject;

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'EDIT':
                return $this->canEdit($message, $user);
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
        return $user === $message->getAuthor();
    }
}

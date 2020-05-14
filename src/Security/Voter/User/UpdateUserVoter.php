<?php

declare(strict_types=1);

namespace App\Security\Voter\User;

use App\Entity\Message;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UpdateUserVoter extends Voter
{
    public const CAN_UPDATE_USER = 'CAN_UPDATE_USER';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        return self::CAN_UPDATE_USER === $attribute && ($subject instanceof User || null === $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {

        // Our previous business logic indicates that mods and admins can do it regardless
        if (\in_array(\implode($token->getRoleNames()), ['ROLE_MODERATOR', 'ROLE_ADMIN', 'ROLE_USER'])) {
            return true;
        }

        // Allow controller handle not found subject
        if (null === $subject) {
            return true;
        }

        $user = $token->getUser();

        if ($user === "anon.") {
            return false;
        }

        // Allow user to update account
        if ($user instanceof User) {
            return $user;
        }

        return false;
    }
}

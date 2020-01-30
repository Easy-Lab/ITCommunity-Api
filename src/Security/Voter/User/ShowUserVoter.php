<?php

declare(strict_types=1);

namespace App\Security\Voter\User;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ShowUserVoter extends Voter
{
    public const CAN_SHOW_ACCOUNT_USER = 'CAN_SHOW_ACCOUNT_USER';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // you only want to vote if the attribute and subject are what you expect
        return self::CAN_SHOW_ACCOUNT_USER === $attribute && ($subject instanceof User || null === $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // our previous business logic indicates that mods and admins can do it regardless
        if (\in_array(\implode($token->getRoleNames()), ['ROLE_MODERATOR', 'ROLE_ADMIN', 'ROLE_USER'])) {
            return true;
        }

        // allow controller handle not found subject
        if (null === $subject) {
            return true;
        }

        $user = $token->getUser();

        // allow user to update account
        if ($user instanceof User) {
            return $user;
        }

        return false;
    }
}

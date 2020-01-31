<?php

declare(strict_types=1);

namespace App\Security\Voter\User\View;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdminViewVoter extends Voter
{
    public const ADMIN_VIEW = 'ADMIN_VIEW';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // you only want to vote if the attribute and subject are what you expect
        return self::ADMIN_VIEW === $attribute && ($subject instanceof User || null === $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // our previous business logic indicates that mods and admins can do it regardless
        if (\in_array(\implode($token->getRoleNames()), ['ROLE_ADMIN'])) {
            return true;
        }

        // allow controller handle not found subject
        if (null === $subject) {
            return true;
        }

        return false;
    }
}
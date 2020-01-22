<?php

declare(strict_types=1);

namespace App\Security\Voter\Review;

use App\Entity\Review;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UpdateReviewVoter extends Voter
{
    public const CAN_UPDATE_REVIEW = 'CAN_UPDATE_REVIEW';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // you only want to vote if the attribute and subject are what you expect
        return self::CAN_UPDATE_REVIEW === $attribute && ($subject instanceof Review || null === $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // our previous business logic indicates that mods and admins can do it regardless
        if (\in_array(\implode($token->getRoleNames()), ['ROLE_MODERATOR', 'ROLE_ADMIN'])) {
            return true;
        }

        // allow controller handle not found subject
        if (null === $subject) {
            return true;
        }

        $user = $token->getUser();

        // allow user to update her review
        if ($user instanceof User) {
            return $subject->getAuthor()->getId() === $user->getId();
        }

        return false;
    }
}

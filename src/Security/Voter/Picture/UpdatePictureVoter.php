<?php

declare(strict_types=1);

namespace App\Security\Voter\Picture;

use App\Entity\Picture;
use App\Entity\Review;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UpdatePictureVoter extends Voter
{
    public const CAN_UPDATE_PICTURE = 'CAN_UPDATE_PICTURE';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        return self::CAN_UPDATE_PICTURE === $attribute && ($subject instanceof Picture || null === $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Our previous business logic indicates that admins can do it regardless
        if (\in_array(\implode($token->getRoleNames()), ['ROLE_MODERATOR', 'ROLE_ADMIN'])) {
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

        // Allow user to delete her picture
        if ($subject instanceof Picture) {
            return $subject->getUser()->getId() === $user->getId();
        }

        return false;
    }
}

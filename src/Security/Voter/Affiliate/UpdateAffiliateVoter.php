<?php


namespace App\Security\Voter\Affiliate;

use App\Entity\Affiliate;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UpdateAffiliateVoter extends Voter
{
    public const CAN_UPDATE_AFFILIATE = 'CAN_UPDATE_AFFILIATE';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        return self::CAN_UPDATE_AFFILIATE === $attribute && ($subject instanceof Affiliate || null === $subject);
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

        if ($subject instanceof Affiliate) {
            return $subject->getUser()->getId() === $user->getId();
        }

        return false;
    }
}
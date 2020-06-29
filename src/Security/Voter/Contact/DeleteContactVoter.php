<?php


namespace App\Security\Voter\Contact;

use App\Entity\Contact;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeleteContactVoter extends Voter
{
    public const CAN_DELETE_CONTACT = 'CAN_DELETE_CONTACT';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        return self::CAN_DELETE_CONTACT === $attribute && ($subject instanceof Contact || null === $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Our previous business logic indicates that admins can do it regardless
        if (\in_array(\implode($token->getRoleNames()), ['ROLE_ADMIN'])) {
            return true;
        }

        // Allow controller handle not found subject
        if (null === $subject) {
            return true;
        }

        return false;
    }
}
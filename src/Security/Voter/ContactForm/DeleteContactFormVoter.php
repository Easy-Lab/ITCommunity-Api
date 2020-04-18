<?php


namespace App\Security\Voter\ContactForm;

use App\Entity\ContactForm;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeleteContactFormVoter extends Voter
{
    public const CAN_DELETE_CONTACT_FORM = 'CAN_DELETE_CONTACT_FORM';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        return self::CAN_DELETE_CONTACT_FORM === $attribute && ($subject instanceof ContactForm || null === $subject);
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
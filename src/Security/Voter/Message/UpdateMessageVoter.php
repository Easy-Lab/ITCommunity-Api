<?php

declare(strict_types=1);

namespace App\Security\Voter\Message;

use App\Entity\Message;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UpdateMessageVoter extends Voter
{
    public const CAN_UPDATE_MESSAGE = 'CAN_UPDATE_MESSAGE';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        return self::CAN_UPDATE_MESSAGE === $attribute && ($subject instanceof Message || null === $subject);
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

        // Allow user to Answer
        if ($subject instanceof Message) {
            return $subject->getUser()->getId() === $user->getId();
        }

        return false;
    }
}

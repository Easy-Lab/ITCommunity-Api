<<<<<<< HEAD
<?php

declare(strict_types=1);

namespace App\Security\Voter\User;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeleteUserVoter extends Voter
{
    public const CAN_DELETE_USER = 'CAN_DELETE_USER';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        return self::CAN_DELETE_USER === $attribute && ($subject instanceof User || null === $subject);
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
=======
<?php

declare(strict_types=1);

namespace App\Security\Voter\User;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DeleteUserVoter extends Voter
{
    public const CAN_DELETE_USER = 'CAN_DELETE_USER';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        return self::CAN_DELETE_USER === $attribute && ($subject instanceof User || null === $subject);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // Our previous business logic indicates that admins can do it regardless
        if (\in_array(\implode($token->getRoleNames()), ['ROLE_ADMIN','ROLE_USER'])) {
            return true;
        }

        // Allow controller handle not found subject
        if (null === $subject) {
            return true;
        }

        return false;
    }
}
>>>>>>> bbfc3fe91d4368442d37b6307557b721492ceeb7

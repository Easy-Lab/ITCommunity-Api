<?php


namespace App\Security\Voter\BugReport;

use App\Entity\BugReport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UpdateBugReportVoter extends Voter
{
    public const CAN_UPDATE_BUG_REPORT = 'CAN_UPDATE_BUG_REPORT';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // You only want to vote if the attribute and subject are what you expect
        return self::CAN_UPDATE_BUG_REPORT === $attribute && ($subject instanceof BugReport || null === $subject);
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

        return false;
    }
}
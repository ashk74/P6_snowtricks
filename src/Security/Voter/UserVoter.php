<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Check user permissions for manage there profile
 */
class UserVoter extends Voter
{
    public const PROFILE = 'USER_PROFILE';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::PROFILE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // If the user is not logged
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof User) {
            throw new \Exception("Type attendu : \App\Entity\User");
        }

        // Return true to grant access
        switch ($attribute) {
            case self::PROFILE:
                return $user === $subject;
                break;
        }

        return false;
    }
}

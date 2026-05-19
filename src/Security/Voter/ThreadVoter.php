<?php

/**
 * Thread voter.
 */

namespace App\Security\Voter;

use App\Entity\Enum\UserRole;
use App\Entity\Thread;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class ThreadVoter.
 */
class ThreadVoter extends Voter
{
    public const EDIT = 'THREAD_EDIT';
    public const DELETE = 'THREAD_DELETE';

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Thread;
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }
        $thread = $subject;
        if (in_array(UserRole::ROLE_ADMIN->value, $user->getRoles(), true)) {
            return true;
        }

        return match ($attribute) {
            self::EDIT, self::DELETE => $this->isAuthor($thread, $user),
            default => false,
        };
    }

    /**
     * @param Thread $thread
     * @param User   $user
     *
     * @return bool
     */
    private function isAuthor(Thread $thread, User $user): bool
    {
        return $thread->getAuthor() === $user;
    }
}

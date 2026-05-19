<?php

/**
 * Comment voter.
 */

namespace App\Security\Voter;

use App\Entity\Enum\UserRole;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CommentVoter.
 */
class CommentVoter extends Voter
{
    public const EDIT = 'COMMENT_EDIT';
    public const DELETE = 'COMMENT_DELETE';

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE]) && $subject instanceof Comment;
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
        $comment = $subject;
        if (in_array(UserRole::ROLE_ADMIN->value, $user->getRoles(), true)) {
            return true;
        }

        return match ($attribute) {
            self::EDIT, self::DELETE => $this->isAuthor($comment, $user),
            default => false,
        };
    }

    /**
     * @param Comment $comment
     * @param User    $user
     *
     * @return bool
     */
    private function isAuthor(Comment $comment, User $user): bool
    {
        return $comment->getAuthor() === $user;
    }
}

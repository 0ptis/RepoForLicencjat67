<?php
/**
 * Class PatchNotesVoter
 */
namespace App\Security\Voter;

use App\Entity\Enum\UserRole;
use App\Entity\PatchNotes;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class PatchNotesVoter
 */
class PatchNotesVoter extends Voter
{
    public const CREATE = 'PATCH_NOTES_CREATE';
    public const DELETE = 'PATCH_NOTES_DELETE';

    /**
     * @param string $attribute
     * @param mixed  $subject
     *
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::CREATE, self::DELETE])) {
            return false;
        }
        if (self::DELETE === $attribute) {
            return $subject instanceof PatchNotes;
        }

        return true;
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
        if (in_array(UserRole::ROLE_ADMIN->value, $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}

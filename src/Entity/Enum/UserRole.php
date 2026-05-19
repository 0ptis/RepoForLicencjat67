<?php

/**
 * User role.
 */

namespace App\Entity\Enum;

/**
 * Enum UserRole.
 */
enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_BLOCKED = 'ROLE_BLOCKED';

    /**
     * Get the role label.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            UserRole::ROLE_USER => 'label.role_user',
            UserRole::ROLE_ADMIN => 'label.role_admin',
            UserRole::ROLE_BLOCKED => 'label.role_blocked',
        };
    }
}

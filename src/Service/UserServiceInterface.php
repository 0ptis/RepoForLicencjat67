<?php

/**
 * UserServiceInterface
 */

namespace App\Service;

use App\Entity\User;

/**
 * Interface UserServiceInterface
 */
interface UserServiceInterface
{
    /**
     * @param User        $user
     * @param string|null $plainPassword
     */
    public function updateUser(User $user, ?string $plainPassword): void;

    /**
     * @param User   $user
     * @param string $plainPassword
     */
    public function createUser(User $user, string $plainPassword): void;
}

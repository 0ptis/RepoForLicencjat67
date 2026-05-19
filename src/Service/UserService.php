<?php

/**
 * User Service
 */

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserService
 */
class UserService implements UserServiceInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param EntityManagerInterface      $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @param User        $user
     * @param string|null $plainPassword
     */
    public function updateUser(User $user, ?string $plainPassword): void
    {
        if (!empty($plainPassword)) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @param User   $user
     * @param string $plainPassword
     */
    public function createUser(User $user, string $plainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}

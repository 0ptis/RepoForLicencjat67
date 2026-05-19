<?php

/**
 * User fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserFixtures.
 */
class UserFixtures extends AbstractBaseFixtures
{
    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Load data.
     *
     * @return void
     */
    protected function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $profilePictures = [
            'DefaultAv.jpg',
            'Profile1.webp',
            'Profile2.jpg',
            'Profile3.jpg',
            'Profile4.png',
            'Profile5.webp',
        ];

        $this->createMany(5, 'users', function (int $i) use ($profilePictures) {
            $user = new User();
            $user->setEmail(sprintf('user%d@vagabond.com', $i));
            $user->setNickname($this->faker->userName());
            $user->setRoles([UserRole::ROLE_USER->value]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'vagabond_1234'
                )
            );
            $user->setProfilePicture($this->faker->randomElement($profilePictures));

            return $user;
        });

        $this->createMany(3, 'admins', function (int $i) use ($profilePictures) {
            $user = new User();
            $user->setEmail(sprintf('admin%d@vagabond.com', $i));
            $user->setNickname($this->faker->userName());
            $user->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'admin_1234'
                )
            );
            $user->setProfilePicture($this->faker->randomElement($profilePictures));

            return $user;
        });

        $this->manager->flush();
    }
}

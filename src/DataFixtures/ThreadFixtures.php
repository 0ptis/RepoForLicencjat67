<?php

/**
 * Thread fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class ThreadFixtures.
 */
class ThreadFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{

    /**
     * Get dependencies.
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    /**
     * Load data.
     *
     * @return void
     */
    protected function loadData(): void
    {
        $this->createMany(15, 'threads', function () {
            $thread = new Thread();
            $thread->setTitle($this->faker->sentence(6));
            $thread->setContent($this->faker->paragraphs(3, true));
            $author = $this->getRandomReference('users', User::class);
            $thread->setAuthor($author);

            return $thread;
        });
    }
}

<?php
/**
 * Comment fixtures
 */
namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Comment fixtures class
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            ThreadFixtures::class,
        ];
    }

    /**
     * @return void
     */
    protected function loadData(): void
    {
        $this->createMany(50, 'comments', function () {
            $comment = new Comment();
            $comment->setContent($this->faker->sentence(12));

            /** @var User $author */
            $author = $this->getRandomReference('users', User::class);
            $comment->setAuthor($author);

            /** @var Thread $thread */
            $thread = $this->getRandomReference('threads', Thread::class);
            $comment->setThread($thread);

            return $comment;
        });
    }
}

<?php
/**
 * Comment Repopsitory
 */
namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * CommentRepotistory Class
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * @param Comment $comment
     * @param bool    $flush
     *
     * @return void
     */
    public function save(Comment $comment, bool $flush = false): void
    {
        $this->getEntityManager()->persist($comment);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Comment $comment
     * @param bool    $flush
     *
     * @return void
     */
    public function delete(Comment $comment, bool $flush = false): void
    {
        $this->getEntityManager()->remove($comment);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}

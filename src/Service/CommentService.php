<?php
/**
 * CommentService
 */
namespace App\Service;

use App\Entity\Comment;
use App\Repository\CommentRepository;

/**
 * Handles persistence operations for comments.
 */
class CommentService implements CommentServiceInterface
{
    /**
     * @param CommentRepository $commentRepository
     */
    public function __construct(private readonly CommentRepository $commentRepository)
    {
    }

    /**
     * @param Comment $comment
     *
     * @return void
     */
    public function save(Comment $comment): void
    {
        $this->commentRepository->save($comment, true);
    }

    /**
     * @param Comment $comment
     *
     * @return void
     */
    public function delete(Comment $comment): void
    {
        $this->commentRepository->delete($comment, true);
    }
}

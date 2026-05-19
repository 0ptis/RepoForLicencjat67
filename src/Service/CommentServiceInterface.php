<?php
/**
 * CommentServiceInterface
 */
namespace App\Service;

use App\Entity\Comment;

/**
 * CommentServiceInterface
 */
interface CommentServiceInterface
{
    /**
     * @param Comment $comment
     *
     * @return void
     */
    public function save(Comment $comment): void;

    /**
     * @param Comment $comment
     *
     * @return void
     */
    public function delete(Comment $comment): void;
}

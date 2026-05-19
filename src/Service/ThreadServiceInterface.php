<?php

/**
 * Thread service interface.
 */

namespace App\Service;

use App\Entity\Thread;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Dto\ThreadListInputFiltersDto;

/**
 * Interface ThreadServiceInterface.
 */
interface ThreadServiceInterface
{
    /**
     * @param int                       $page
     * @param ThreadListInputFiltersDto $filters
     *
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page, ThreadListInputFiltersDto $filters): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Thread $thread
     */
    public function save(Thread $thread): void;

    /**
     * Delete entity.
     *
     * @param Thread $thread
     */
    public function delete(Thread $thread): void;
}

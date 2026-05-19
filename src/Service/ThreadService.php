<?php

/**
 * Thread service.
 */

namespace App\Service;

use App\Entity\Thread;
use App\Repository\ThreadRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Dto\ThreadListInputFiltersDto;

/**
 * Class ThreadService.
 */
class ThreadService implements ThreadServiceInterface
{
    /**
     * @varant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ThreadRepository   $threadRepository
     * @param PaginatorInterface $paginator
     */
    public function __construct(private readonly ThreadRepository $threadRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * @param int $page
     * @param $filters
     *
     * @return PaginationInterface
     */
    public function getPaginatedList(int $page, ThreadListInputFiltersDto $filters): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->threadRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Thread $thread
     */
    public function save(Thread $thread): void
    {
        $this->threadRepository->save($thread);
    }

    /**
     * Delete entity.
     *
     * @param Thread $thread
     */
    public function delete(Thread $thread): void
    {
        $this->threadRepository->delete($thread);
    }
}

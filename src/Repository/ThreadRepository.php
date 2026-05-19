<?php

/**
 * Thread repository.
 */

namespace App\Repository;

use App\Entity\Thread;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use App\Dto\ThreadListInputFiltersDto;

/**
 * Class ThreadRepository.
 */
class ThreadRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thread::class);
    }

    /**
     * Query all threads.
     * @param ThreadListInputFiltersDto $filters
     *
     * @return QueryBuilder
     */
    public function queryAll(ThreadListInputFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('thread')
            ->select('thread', 'author')
            ->leftJoin('thread.author', 'author')
            ->orderBy('thread.createdAt', 'DESC');

        if (null !== $filters->search && '' !== trim($filters->search)) {
            $searchTerm = '%'.mb_strtolower(trim($filters->search)).'%';

            $queryBuilder->andWhere('LOWER(thread.title) LIKE :search OR LOWER(thread.content) LIKE :search')
                ->setParameter('search', $searchTerm);
        }

        return $queryBuilder;
    }

    /**
     * Save entity.
     *
     * @param Thread $thread
     */
    public function save(Thread $thread): void
    {
        $this->getEntityManager()->persist($thread);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete entity.
     *
     * @param Thread $thread
     */
    public function delete(Thread $thread): void
    {
        $this->getEntityManager()->remove($thread);
        $this->getEntityManager()->flush();
    }
}

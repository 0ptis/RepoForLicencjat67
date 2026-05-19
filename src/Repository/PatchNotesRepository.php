<?php

/**
 * Patch notes repository.
 */

namespace App\Repository;

use App\Dto\PatchNotesFiltersDto;
use App\Entity\PatchNotes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class PatchNotesRepository.
 */
class PatchNotesRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PatchNotes::class);
    }

    /**
     * Find by filters.
     *
     * @param PatchNotesFiltersDto $filters
     *
     * @return array
     */
    public function findByFilters(PatchNotesFiltersDto $filters): array
    {
        $qb = $this->createQueryBuilder('p');
        if (!empty($filters->search)) {
            $qb->andWhere('p.title LIKE :search OR p.content LIKE :search')
                ->setParameter('search', '%'.$filters->search.'%');
        }
        $qb->orderBy('p.createdAt', $filters->sort);

        return $qb->getQuery()->getResult();
    }
}

<?php

/*
 * User Repository
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * Class UserRepository
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * Constructor for user repository.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string|null $query
     *
     * @return array
     */
    public function findUser(?string $query): array
    {
        $qb = $this->createQueryBuilder('u');

        if ($query) {
            $qb->andWhere('u.email LIKE :query OR u.nickname LIKE :query')
                ->setParameter('query', '%'.$query.'%');
        }

        $qb->orderBy('u.nickname', 'ASC');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param PasswordAuthenticatedUserInterface $user
     * @param string                             $newHashedPassword
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }
}

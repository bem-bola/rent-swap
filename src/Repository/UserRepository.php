<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorService $paginatorService
    )
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
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

    public function getAll(array $queryParams, User $user):array {

        $queryBuilder = $this->createQueryBuilder('u');

        if(!in_array('ROLE_ADMIN', $user->getRoles())) {
            $queryBuilder
                ->andWhere('u.roles NOT LIKE :adminRole')
                ->andWhere('u.roles NOT LIKE :moderatorRole')
                ->setParameter('adminRole', '%ROLE_ADMIN%')
                ->setParameter('moderatorRole', '%ROLE_MODERATOR%');

        }

        return $this->paginatorService->dataTableByQueryBuilder(
            $queryBuilder,
            $queryParams,
        );
    }

    public function length(User $user): ?int{

        $queryBuilder = $this->createQueryBuilder('u');

        if(!in_array('ROLE_ADMIN', $user->getRoles())) {
            $queryBuilder
                ->andWhere('u.roles NOT LIKE :adminRole')
                ->andWhere('u.roles NOT LIKE :moderatorRole')
                ->setParameter('adminRole', '%ROLE_ADMIN%')
                ->setParameter('moderatorRole', '%ROLE_MODERATOR%');

        }
        return $queryBuilder
            ->select('COUNT(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

}

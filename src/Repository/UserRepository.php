<?php

namespace App\Repository;

use App\Entity\User;
use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Exception;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[] Array of online users
     */
    public function findOnlineUsers(): array
    {
        return $this->onlineUsersQb()->getQuery()->getResult();
    }

    /**
     * @return int Number of online users
     */
    public function countOnlineUsers(): int
    {
        try {
            return (int)$this->onlineUsersQb()
                ->select('COUNT(u.id)')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * @return User|null Return the last registered user or null if there is none
     */
    public function findLastRegistered(): ?User
    {
        return $this->findOneBy([], ['createdAt' => 'DESC']);
    }

    /**
     * @param string $role
     * @return User[]
     */
    public function findByRole(string $role): array
    {
        return $this->joinMessagesQb()
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"' . $role . '"%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    private function onlineUsersQb(): QueryBuilder
    {
        $comparisonDate = (new DateTime())
            ->sub(new DateInterval('PT15M'));

        return $this->createQueryBuilder('u')
            ->where('u.lastActivityAt > :date')
            ->setParameter('date', $comparisonDate)
            ->orderBy('u.lastActivityAt', 'DESC');
    }

    /**
     * @return QueryBuilder
     */
    public function findAllMembersQb(): QueryBuilder
    {
        return $this->joinMessagesQb()
            ->orderBy('u.pseudo');
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    public function joinMessagesQb(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQb($qb)
            ->leftJoin('u.messages', 'm')
            ->addSelect('m');
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function getOrCreateQb(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('u');
    }
}

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
     * @return QueryBuilder
     * @throws Exception
     */
    private function getOnlineUsers(): QueryBuilder
    {
        $currentDate = new DateTime();
        $currentDate->sub(new DateInterval('PT15M'));

        return $this->createQueryBuilder('u')
            ->where('u.lastActivityAt > :date')
            ->setParameter('date', $currentDate);
    }

    /**
     * @return User[] Array of online users
     * @throws Exception
     */
    public function findOnlineUsers(): array
    {
        return $this->getOnlineUsers()
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return int Number of online users
     */
    public function countOnlineUsers(): int
    {
        try {
            return (int)$this->getOnlineUsers()
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
        return $this->findOneBy([], ['registrationDate' => 'DESC']);
    }

    /**
     * @param $role
     * @return User[]
     */
    public function findByRole($role): array
    {
        return $this->createQueryBuilder('u')
            ->addSelect('m')
            ->innerJoin('u.messages', 'm')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"' . $role . '"%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return QueryBuilder
     */
    public function findAllMembersQb(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->addSelect('m')
            ->leftJoin('u.messages', 'm')
            ->orderBy('u.pseudo');
    }
}

<?php

namespace App\Repository;

use App\Entity\User;
use DateInterval;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return QueryBuilder
     * @throws Exception
     */
    private function getOnlineUsers()
    {
        $currentDate = new \DateTime();
        $currentDate->sub(new DateInterval('PT15M'));

        return $this->createQueryBuilder('u')
            ->where('u.lastActivityAt > :date')
            ->setParameter('date', $currentDate);
    }

    /**
     * @return User[] Returns an array of User objects
     * @throws Exception
     */
    public function findOnlineUsers()
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
     * @return User|null Return the last registered user (or null)
     */
    public function findLastRegistered()
    {
        return $this->findOneBy([], ['registrationDate' => 'DESC']);
    }

    /**
     * @param $role
     * @return User[]
     */
    public function findByRole($role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"' . $role . '"%')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

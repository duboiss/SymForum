<?php

namespace App\Repository;

use App\Entity\Forum;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Thread|null find($id, $lockMode = null, $lockVersion = null)
 * @method Thread|null findOneBy(array $criteria, array $orderBy = null)
 * @method Thread[]    findAll()
 * @method Thread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Thread::class);
    }

    /**
     * @param User $user
     * @param $limit
     * @return Thread[]
     */
    public function findLastThreadsByUser(User $user, $limit)
    {
        return $this->findBy(['author' => $user], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * @param Forum $forum
     * @return Thread[]
     */
    public function findThreadsByForum(Forum $forum)
    {
        return $this->findBy(['forum' => $forum], ['createdAt' => 'DESC']);
    }

    // /**
    //  * @return Thread[] Returns an array of Thread objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Thread
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

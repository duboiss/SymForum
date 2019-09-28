<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param User $user
     * @param $limit
     * @return Message[]
     */
    public function findLastMessagesByUser(User $user, $limit)
    {
        return $this->findBy(['author' => $user], ['publishedAt' => 'DESC'], $limit);
    }

    /**
     * @param Thread $thread
     * @return Message[]
     */
    public function findMessagesByThreadWithAuthor(Thread $thread)
    {
        return $this->createQueryBuilder('m')
            ->select('m', 'author')
            ->leftJoin('m.author', 'author')
            ->where('m.thread = :thread')
            ->setParameter('thread', $thread)
            ->orderBy('m.publishedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

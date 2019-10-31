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
     * @return Message|null
     */
    public function findLastMessageByUser(User $user): ?Message
    {
        return $this->findOneBy(['author' => $user], ['publishedAt' => 'DESC']);
    }

    /**
     * @param User $user
     * @param int $limit
     * @return Message[]
     */
    public function findLastMessagesByUser(User $user, int $limit): array
    {
        return $this->findBy(['author' => $user], ['publishedAt' => 'DESC'], $limit);
    }

    /**
     * @param Thread $thread
     * @return Message|null
     */
    public function findLastMessageByThread(Thread $thread): ?Message
    {
        return $this->findOneBy(['thread' => $thread], ['publishedAt' => 'DESC']);
    }

    /**
     * @param Thread $thread
     * @return Message[]
     */
    public function findMessagesByThreadWithAuthor(Thread $thread): array
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
}

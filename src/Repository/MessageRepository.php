<?php

namespace App\Repository;

use App\Entity\Forum;
use App\Entity\Message;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\UnexpectedResultException;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param User $user
     * @return Message|null
     */
    public function findLastMessageByUser(User $user): ?Message
    {
        return $this->findOneBy(['author' => $user], ['createdAt' => 'DESC']);
    }

    /**
     * @param User $user
     * @param int $limit
     * @return Message[]
     */
    public function findLastMessagesByUser(User $user, int $limit): array
    {
        return $this->findBy(['author' => $user], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * @param Thread $thread
     * @return Message|null
     */
    public function findLastMessageByThread(Thread $thread): ?Message
    {
        return $this->findOneBy(['thread' => $thread], ['createdAt' => 'DESC']);
    }

    /**
     * @param Thread $thread
     * @param bool $onlyId
     * @return Message[]
     */
    public function findMessagesByThread(Thread $thread, bool $onlyId = false): array
    {
        $qb = $this->createQueryBuilder('m');

        if ($onlyId) {
            $qb->select('m.id');
        }

        $this->whereThreadQb($thread, $qb);
        $qb = $qb->orderBy('m.createdAt', 'ASC')
            ->getQuery()
            ->getResult();

        return $onlyId ? array_column($qb, 'id') : $qb;
    }

    /**
     * @param Thread $thread
     * @return QueryBuilder
     */
    public function findMessagesByThreadWithAuthorQb(Thread $thread): QueryBuilder
    {
        return $this->whereThreadQb($thread)
            ->leftJoin('m.author', 'author')
            ->addSelect('author')
            ->orderBy('m.createdAt', 'ASC');
    }

    /**
     * @param Forum $forum
     * @return Message|null
     */
    public function findLastMessageByForum(Forum $forum): ?Message
    {
        try {
            return $this->joinThreadQb()
                ->where('thread.forum = :forum')
                ->setParameter(':forum', $forum)
                ->orderBy('m.createdAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param Thread $thread
     * @return Message
     */
    public function findFirstMessageInThread(Thread $thread): Message
    {
        try {
            return $this->whereThreadQb($thread)
                ->orderBy('m.createdAt', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch (UnexpectedResultException $e) {
            throw new $e;
        }
    }

    /**
     * @param Message $message
     * @return Message|null
     */
    public function findNextMessageInThread(Message $message): ?Message
    {
        try {
            return $this->whereThreadQb($message->getThread())
                ->andWhere('m.createdAt > :message')
                ->setParameter(':message', $message->getCreatedAt())
                ->orderBy('m.createdAt', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param User $user
     * @return QueryBuilder
     */
    public function findMessagesByUserQb(User $user): QueryBuilder
    {
        return $this->joinThreadQb()
            ->where('m.author = :user')
            ->orderBy('m.createdAt', 'DESC')
            ->setParameter('user', $user);
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function joinThreadQb(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQb($qb)
            ->join('m.thread', 'thread')
            ->addSelect('thread');
    }

    /**
     * @param Thread $thread
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function whereThreadQb(Thread $thread, QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQb($qb)
            ->andWhere('m.thread = :thread')
            ->setParameter(':thread', $thread);
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function getOrCreateQb(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('m');
    }
}

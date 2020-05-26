<?php

namespace App\Repository;

use App\Entity\Forum;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Thread|null find($id, $lockMode = null, $lockVersion = null)
 * @method Thread|null findOneBy(array $criteria, array $orderBy = null)
 * @method Thread[]    findAll()
 * @method Thread[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Thread::class);
    }

    /**
     * @param User $user
     * @return Thread|null
     */
    public function findLastThreadByUser(User $user): ?Thread
    {
        return $this->findOneBy(['author' => $user], ['createdAt' => 'DESC']);
    }

    /**
     * @param User $user
     * @param int $limit
     * @return Thread[]
     */
    public function findLastThreadsByUser(User $user, int $limit): array
    {
        return $this->findBy(['author' => $user], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * @param Forum $forum
     * @return QueryBuilder
     */
    public function findThreadsByForumQb(Forum $forum): QueryBuilder
    {
        return $this->addLastMessageQb()
            ->addSelect('t', 'author')
            ->leftJoin('t.author', 'author')
            ->where('t.forum = :forum')
            ->orderBy('t.isPin', 'DESC')
            ->addOrderBy('lm.createdAt', 'DESC')
            ->setParameter('forum', $forum);
    }

    /**
     * @param User $user
     * @return QueryBuilder
     */
    public function findThreadsByUserQb(User $user): QueryBuilder
    {
        return $this->addLastMessageQb()
            ->where('t.author = :user')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user);
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    public function addLastMessageQb(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQb($qb)
            ->addSelect('t', 'lm')
            ->addSelect('t', 'lmAuthor')
            ->join('t.lastMessage', 'lm')
            ->leftJoin('lm.author', 'lmAuthor');
    }

    /**
     * @param QueryBuilder|null $qb
     * @return QueryBuilder
     */
    private function getOrCreateQb(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('t');
    }
}

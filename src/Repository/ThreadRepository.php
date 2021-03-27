<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Forum;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

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

    public function findLastThreadByUser(User $user): ?Thread
    {
        return $this->findOneBy(['author' => $user], ['createdAt' => 'DESC']);
    }

    /**
     * @return Thread[]
     */
    public function findLastThreadsByUser(User $user, int $limit): array
    {
        return $this->findBy(['author' => $user], ['createdAt' => 'DESC'], $limit);
    }

    public function findThreadsByForumQb(Forum $forum): QueryBuilder
    {
        return $this->joinLastMessageQb()
            ->leftJoin('t.author', 'author')
            ->addSelect('author')
            ->where('t.forum = :forum')
            ->orderBy('t.isPin', 'DESC')
            ->addOrderBy('lm.createdAt', 'DESC')
            ->setParameter('forum', $forum)
        ;
    }

    public function findThreadsByUserQb(User $user): QueryBuilder
    {
        return $this->joinLastMessageQb()
            ->where('t.author = :user')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
        ;
    }

    public function joinLastMessageQb(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQb($qb)
            ->join('t.lastMessage', 'lm')
            ->leftJoin('lm.author', 'lmAuthor')
            ->addSelect('lm', 'lmAuthor')
        ;
    }

    private function getOrCreateQb(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('t');
    }
}

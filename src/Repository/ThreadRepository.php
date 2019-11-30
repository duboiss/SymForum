<?php

namespace App\Repository;

use App\Entity\Forum;
use App\Entity\Thread;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
     * @param $limit
     * @return Thread[]
     */
    public function findLastThreadsByUser(User $user, int $limit): array
    {
        return $this->findBy(['author' => $user], ['createdAt' => 'DESC'], $limit);
    }

    /**
     * @param Forum $forum
     * @return array
     */
    public function findThreadsByForum(Forum $forum): array
    {
        return $this->createQueryBuilder('t')
            ->addSelect('t', 'author')
            ->addSelect('t', 'lm')
            ->addSelect('t', 'lmAuthor')
            ->leftJoin('t.author', 'author')
            ->join('t.lastMessage', 'lm')
            ->leftJoin('lm.author', 'lmAuthor')
            ->where('t.forum = :forum')
            ->orderBy('lm.publishedAt', 'DESC')
            ->setParameter('forum', $forum)
            ->getQuery()
            ->getResult();
    }
}

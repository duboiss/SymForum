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
        $query = $this->createQueryBuilder('t')
            ->addSelect('t', 'author')
            ->addSelect('MAX(m.publishedAt) AS lastMessage')
            ->addSelect('COUNT(m.id) AS nbMessages')
            ->leftJoin('t.author', 'author')
            ->join('t.messages', 'm')
            ->where('t.forum = :forum')
            ->groupBy('t.id')
            ->orderBy('lastMessage', 'DESC')
            ->setParameter('forum', $forum)
            ->getQuery()
            ->getResult();

        return array_map(function ($value) {
            return [
                'thread' => $value[0],
                'lastMessage' => $value['lastMessage'],
                'nbMessages' => $value['nbMessages']
            ];
        }, $query);
    }
}

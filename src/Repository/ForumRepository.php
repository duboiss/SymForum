<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Forum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Forum|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forum|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forum[]    findAll()
 * @method Forum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forum::class);
    }

    /**
     * @return Forum[]
     */
    public function findForumsByCategory(Category $category): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.lastMessage', 'lm')
            ->leftJoin('lm.author', 'lmAuthor')
            ->leftJoin('lm.thread', 'lmThread')
            ->addSelect('lm, lmAuthor, lmThread')
            ->where('f.category = :category')
            ->setParameter('category', $category)
            ->orderBy('f.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Forum[]
     */
    public function findForumsWithCategories(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.category', 'cat')
            ->addSelect('cat')
            ->orderBy('f.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}

<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Forum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

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
     * @param Category $category
     * @return Forum[]
     */
    public function findForumsByCategory(Category $category): array
    {
        return $this->createQueryBuilder('f')
            ->addSelect('f', 'lm')
            ->addSelect('f', 'lmAuthor')
            ->addSelect('f', 'lmThread')
            ->leftJoin('f.lastMessage', 'lm')
            ->leftJoin('lm.author', 'lmAuthor')
            ->leftJoin('lm.thread', 'lmThread')
            ->where('f.category = :category')
            ->setParameter(':category', $category)
            ->orderBy('f.position', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Forum[]
     */
    public function findForumsWithCategories(): array
    {
        return $this->createQueryBuilder('f')
            ->addSelect('f', 'category')
            ->join('f.category', 'category')
            ->orderBy('f.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

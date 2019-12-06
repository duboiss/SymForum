<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     */
    public function findAllCategories(): array
    {
        return $this->createQueryBuilder('c')
            ->addSelect('c', 'forums')
            ->addSelect('c', 'lm')
            ->addSelect('c', 'lmAuthor')
            ->addSelect('c', 'lmThread')
            ->join('c.forums', 'forums')
            ->leftJoin('forums.lastMessage', 'lm')
            ->leftJoin('lm.author', 'lmAuthor')
            ->leftJoin('lm.thread', 'lmThread')
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

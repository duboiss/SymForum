<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
            ->join('c.forums', 'f')
            ->leftJoin('f.lastMessage', 'lm')
            ->leftJoin('lm.author', 'lmAuthor')
            ->leftJoin('lm.thread', 'lmThread')
            ->addSelect('f, lm, lmAuthor, lmThread')
            ->orderBy('c.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

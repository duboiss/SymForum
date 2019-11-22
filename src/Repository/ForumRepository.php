<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Forum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Forum|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forum|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forum[]    findAll()
 * @method Forum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
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
            ->join('f.lastMessage', 'lm')
            ->leftJoin('lm.author', 'lmAuthor')
            ->where('f.category = :category')
            ->setParameter(':category', $category)
            ->orderBy('f.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

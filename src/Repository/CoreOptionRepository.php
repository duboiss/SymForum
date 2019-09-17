<?php

namespace App\Repository;

use App\Entity\CoreOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CoreOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoreOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoreOption[]    findAll()
 * @method CoreOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoreOptionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CoreOption::class);
    }

    // /**
    //  * @return CoreOption[] Returns an array of CoreOption objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CoreOption
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

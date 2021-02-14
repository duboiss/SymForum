<?php

namespace App\Repository;

use App\Entity\CoreOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CoreOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method CoreOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method CoreOption[]    findAll()
 * @method CoreOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoreOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CoreOption::class);
    }

    public function findCoreOptionByName(string $name): ?CoreOption
    {
        return $this->findOneBy(['name' => $name]);
    }
}

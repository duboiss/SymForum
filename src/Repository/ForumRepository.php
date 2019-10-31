<?php

namespace App\Repository;

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
     * @param Forum $forum
     * @return Forum[]
     */
    public function findSubforumsByParent(Forum $forum): array
    {
        return $this->findBy(['parent' => $forum], ['position' => 'ASC']);
    }
}

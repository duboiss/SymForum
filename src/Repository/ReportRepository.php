<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Report|null find($id, $lockMode = null, $lockVersion = null)
 * @method Report|null findOneBy(array $criteria, array $orderBy = null)
 * @method Report[]    findAll()
 * @method Report[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function countUntreatedReports(): int
    {
        try {
            return (int) $this->createQueryBuilder('r')
                ->select('COUNT(r.id)')
                ->where('r.treatedAt is NULL')
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (Exception) {
            return 0;
        }
    }

    public function findAllReportsQb(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.reportedBy', 'reportedBy')
            ->join('r.message', 'm')
            ->leftJoin('m.author', 'mAuthor')
            ->addSelect('reportedBy', 'm', 'mAuthor')
            ->orderBy('r.createdAt', 'DESC')
        ;
    }

    /**
     * @return Report[]
     */
    public function findByMessage(Message $message, Report $except = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.message = :message')
            ->setParameter('message', $message)
        ;

        if ($except) {
            $qb->andWhere('r != :except')
                ->setParameter('except', $except)
            ;
        }

        return $qb->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}

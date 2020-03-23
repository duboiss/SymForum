<?php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\Report;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
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

    /**
     * @return int Number of untreated reports
     */
    public function countUntreatedReports(): int
    {
        try {
            return (int)$this->createQueryBuilder('r')
                ->select('COUNT(r.id)')
                ->where('r.treatedAt is NULL')
                ->getQuery()
                ->getSingleScalarResult();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * @return QueryBuilder
     */
    public function findAllReportsQb(): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->addSelect('r', 'reportedBy')
            ->addSelect('r', 'm')
            ->addSelect('r', 'messageAuthor')
            ->leftJoin('r.reportedBy', 'reportedBy')
            ->join('r.message', 'm')
            ->leftJoin('m.author', 'messageAuthor')
            ->orderBy('r.reportedAt', 'DESC');
    }


    /**
     * @param Message $message
     * @param int $except
     * @return Report[]
     */
    public function findByMessage(Message $message, int $except = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.message = :message')
            ->setParameter('message', $message);

        if ($except) {
            $qb->andWhere('r.id != :except')
                ->setParameter('except', $except);
        }

        return $qb->orderBy('r.reportedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

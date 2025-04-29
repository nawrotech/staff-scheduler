<?php

namespace App\Repository;

use App\Entity\Assignment;
use App\Entity\Shift;
use App\Enum\AssignmentStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Assignment>
 */
class AssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assignment::class);
    }

    public function getPositionCountsForShift(Shift $shift, AssignmentStatus $status): array
    {
        $results = $this->createQueryBuilder('a')
            ->select('sp.id as positionId, COUNT(a.id) as count')
            ->join('a.shiftPosition', 'sp')
            ->where('a.shift = :shift')
            ->andWhere('a.status = :status')
            ->setParameter('shift', $shift)
            ->setParameter('status', $status)
            ->groupBy('sp.id')
            ->getQuery()
            ->getResult();

        return array_column($results, 'count', 'positionId');
    }

    //    /**
    //     * @return Assignment[] Returns an array of Assignment objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Assignment
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

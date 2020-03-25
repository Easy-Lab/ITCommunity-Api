<?php

namespace App\Repository;

use App\Entity\Point;
use App\Interfaces\RepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Point|null find($id, $lockMode = null, $lockVersion = null)
 * @method Point|null findOneBy(array $criteria, array $orderBy = null)
 * @method Point[]    findAll()
 * @method Point[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PointRepository extends AbstractRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Point::class);
    }

    public function topUser($limit = null) {
        return $this->createQueryBuilder('p')
            ->select('p as points, SUM(p.amount) as total_points')
            ->join('p.user', 'u')
            ->groupBy('p.user')
            ->orderBy('total_points', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}

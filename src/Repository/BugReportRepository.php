<?php

namespace App\Repository;

use App\Entity\BugReport;
use App\Interfaces\RepositoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BugReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method BugReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method BugReport[]    findAll()
 * @method BugReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BugReportRepository extends AbstractRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BugReport::class);
    }
}

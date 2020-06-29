<?php

namespace App\Repository;

use App\Entity\Affiliate;
use App\Interfaces\RepositoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Affiliate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Affiliate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Affiliate[]    findAll()
 * @method Affiliate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AffiliateRepository extends AbstractRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Affiliate::class);
    }
}

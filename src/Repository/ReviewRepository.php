<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Review;
use App\Interfaces\RepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * {@inheritdoc}
 *
 * @method Review find($id, $lockMode = null, $lockVersion = null)
 * @method Review findOneBy(array $criteria, array $orderBy = null)
 * @method Review[] findAll()
 */
class ReviewRepository extends AbstractRepository implements RepositoryInterface
{
    /**
     * ReviewRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }
}

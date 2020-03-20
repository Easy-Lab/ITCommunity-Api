<?php

namespace App\Repository;

use App\Entity\PublicChat;
use App\Interfaces\RepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PublicChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicChat[]    findAll()
 * @method PublicChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicChatRepository extends AbstractRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicChat::class);
    }
}

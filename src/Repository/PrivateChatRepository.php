<?php

namespace App\Repository;

use App\Entity\PrivateChat;
use App\Interfaces\RepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrivateChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivateChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivateChat[]    findAll()
 * @method PrivateChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivateChatRepository extends AbstractRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateChat::class);
    }
}

<?php

namespace App\Repository;

use App\Entity\PrivateChat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PrivateChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivateChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivateChat[]    findAll()
 * @method PrivateChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivateChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateChat::class);
    }

    // /**
    //  * @return PrivateChat[] Returns an array of PrivateChat objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PrivateChat
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

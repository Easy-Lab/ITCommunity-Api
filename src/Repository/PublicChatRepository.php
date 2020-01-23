<?php

namespace App\Repository;

use App\Entity\PublicChat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PublicChat|null find($id, $lockMode = null, $lockVersion = null)
 * @method PublicChat|null findOneBy(array $criteria, array $orderBy = null)
 * @method PublicChat[]    findAll()
 * @method PublicChat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PublicChatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PublicChat::class);
    }

    // /**
    //  * @return PublicChat[] Returns an array of PublicChat objects
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
    public function findOneBySomeField($value): ?PublicChat
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

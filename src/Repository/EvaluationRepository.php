<?php

namespace App\Repository;

use App\Entity\Contact;
use App\Entity\Evaluation;
use App\Entity\User;
use App\Interfaces\RepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Evaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluation[]    findAll()
 * @method Evaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationRepository extends AbstractRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    public function findEvaluationExist(User $user, Contact $contact)
    {
        return $this->createQueryBuilder('e')
            ->where('e.user = :user')
            ->andWhere('e.contact = :contact')
            ->setParameter('user',$user)
            ->setParameter('contact',$contact)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }
}

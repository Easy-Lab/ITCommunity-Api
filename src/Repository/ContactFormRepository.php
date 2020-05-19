<?php

namespace App\Repository;

use App\Entity\ContactForm;
use App\Interfaces\RepositoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ContactForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactForm[]    findAll()
 * @method ContactForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactFormRepository extends AbstractRepository implements RepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactForm::class);
    }
}

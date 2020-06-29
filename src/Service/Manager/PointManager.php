<?php

declare(strict_types=1);

namespace App\Service\Manager;

use App\Controller\AbstractController;
use App\Entity\Point;
use App\Repository\PointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class PointManager
{
    /**
     * @var EncoderFactoryInterface
     */
    protected $encoderFactory;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var PointRepository
     */
    protected $repository;


    /**
     * UserManager constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param EntityManagerInterface $entityManager
     * @param PointRepository $pointRepository
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager,
        PointRepository $pointRepository
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
        $this->repository = $pointRepository;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return Point::class;
    }

    /**
     * @param array $criteria
     *
     * @return object
     */
    public function findEvaluationBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @param array $criteria
     *
     * @return object
     */
    public function findEvaluationsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * @return array
     */
    public function findEvaluations()
    {
        return $this->repository->findAll();
    }

    /**
     * @return array
     */
    public function topUser()
    {
        return $this->repository->topUser();
    }
}
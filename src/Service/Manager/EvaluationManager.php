<?php

declare(strict_types=1);

namespace App\Service\Manager;

use App\Controller\AbstractController;
use App\Entity\Evaluation;
use App\Repository\EvaluationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\UserService;

class EvaluationManager
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
     * @var EvaluationRepository
     */
    protected $repository;

    /**
     * @var UserService
     */
    protected $userService;


    /**
     * UserManager constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param EntityManagerInterface $entityManager
     * @param EvaluationRepository $evaluationRepository
     * @param UserService $userService
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager,
        EvaluationRepository $evaluationRepository,
        UserService $userService
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
        $this->repository = $evaluationRepository;
        $this->userService = $userService;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return Evaluation::class;
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
     * @return array
     */
    public function findEvaluations()
    {
        return $this->repository->findAll();
    }
}
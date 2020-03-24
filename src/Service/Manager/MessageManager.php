<?php

declare(strict_types=1);

namespace App\Service\Manager;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class MessageManager
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
     * @var MessageRepository
     */
    protected $repository;


    /**
     * UserManager constructor.
     *
     * @param EncoderFactoryInterface $encoderFactory
     * @param EntityManagerInterface $entityManager
     * @param MessageRepository $messageRepository
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager,
        MessageRepository $messageRepository
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
        $this->repository = $messageRepository;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return Message::class;
    }

    /**
     * @param array $criteria
     *
     * @return object
     */
    public function findMessageBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return array
     */
    public function findMessages()
    {
        return $this->repository->findAll();
    }
}
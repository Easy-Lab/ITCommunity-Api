<?php

declare(strict_types=1);

namespace App\Service\Manager;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserManager
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
     * @var UserRepository
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
     * @param UserRepository $userRepository
     * @param UserService $userService
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserService $userService
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
        $this->repository = $userRepository;
        $this->userService = $userService;
    }

    /**
     * @param UserInterface $user
     */
    public function deleteUser(UserInterface $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return User::class;
    }

    /**
     * @param array $criteria
     *
     * @return object
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return array
     */
    public function findUsers()
    {
        return $this->repository->findAll();
    }

    /**
     * @param UserInterface $user
     */
    public function reloadUser(UserInterface $user)
    {
        $this->entityManager->refresh($user);
    }

    /**
     * Finds a user by email.
     *
     * @param string $email
     *
     * @return object|User|UserInterface
     */
    public function findUserByEmail($email)
    {
        foreach ($this->findUsers() as $user) {
            $userDecrypte = $this->userService->getUncrypted($user, 'email');

            if ($userDecrypte === $email) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Finds a user by email.
     *
     * @param $username
     * @return object|User|UserInterface
     */
    public function findUserByUsername($username)
    {
        foreach ($this->findUsers() as $user) {
            if ($user->getUsername() === $username) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Finds a user by hash.
     *
     * @param $hash
     * @return object|User|UserInterface
     */
    public function findUserByHash($hash)
    {
        foreach ($this->findUsers() as $user) {
            if ($user->getHash() === $hash) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Finds a user by email or username.
     *
     * @param $param
     *
     * @return object|User|UserInterface
     */
    public function findUserByEmailOrUsername($param)
    {

        foreach ($this->findUsers() as $user) {
            $userDecrypte = $this->userService->getUncrypted($user, 'email');

            if ($userDecrypte === $param) {
                return $user;
            }

            if ($user->getUsername() === $param) {
                return $user;
            }
        }

        return null;
    }
}

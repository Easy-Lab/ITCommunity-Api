<?php

declare(strict_types=1);

namespace App\Service\Manager;

use App\Controller\AbstractController;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\UserService;

class ContactManager
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
     * @var ContactRepository
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
     * @param ContactRepository $contactRepository
     * @param UserService $userService
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory,
        EntityManagerInterface $entityManager,
        ContactRepository $contactRepository,
        UserService $userService
    )
    {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
        $this->repository = $contactRepository;
        $this->userService = $userService;
    }

    /**
     * @param UserInterface $contact
     */
    public function deleteContact(UserInterface $contact)
    {
        $this->entityManager->remove($contact);
        $this->entityManager->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return Contact::class;
    }

    /**
     * @param array $criteria
     *
     * @return object
     */
    public function findContactBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return array
     */
    public function findContacts()
    {
        return $this->repository->findAll();
    }

    /**
     * @param UserInterface $contact
     */
    public function reloadContact(UserInterface $contact)
    {
        $this->entityManager->refresh($contact);
    }

    /**
     * Finds a contact by email.
     *
     * @param string $email
     *
     * @return object|Contact
     */
    public function findContactByEmail($email)
    {
        foreach ($this->findContacts() as $contact) {
            if ($contact->getEmail() === $email) {
                return $contact;
            }
        }

        return null;
    }
}
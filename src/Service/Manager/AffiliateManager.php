<?php

declare(strict_types=1);

namespace App\Service\Manager;

use App\Controller\AbstractController;
use App\Entity\Affiliate;
use App\Repository\AffiliateRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class AffiliateManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var AffiliateRepository
     */
    protected $repository;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserManager constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param AffiliateRepository $affiliateRepository
     * @param UserService $userService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AffiliateRepository $affiliateRepository,
        UserService $userService
    )
    {
        $this->entityManager = $entityManager;
        $this->repository = $affiliateRepository;
        $this->userService = $userService;
    }

    /**
     * @param Affiliate $affiliate
     */
    public function deleteAffiliate(Affiliate $affiliate)
    {
        $this->entityManager->remove($affiliate);
        $this->entityManager->flush();
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return Affiliate::class;
    }

    /**
     * @param array $criteria
     *
     * @return object
     */
    public function findAffiliateBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * @return array
     */
    public function findAffiliates()
    {
        return $this->repository->findAll();
    }

    /**
     * @param Affiliate $affiliate
     */
    public function reloadAffiliate(Affiliate $affiliate)
    {
        $this->entityManager->refresh($affiliate);
    }

    /**
     * Finds a user by cripted email.
     *
     * @param string $email
     *
     * @return object|Affiliate
     */
    public function findAffiliateByEmail($email)
    {
        foreach ($this->findAffiliates() as $affiliate) {
            if ($affiliate->getEmail() === $email) {
                return $affiliate;
            }
        }

        return null;
    }

    /**
     * Finds a user by email.
     *
     * @param string $email
     *
     * @return object|Affiliate
     */
    public function findAffiliateByCriptedEmail($email)
    {
        foreach ($this->findAffiliates() as $affiliate) {
            $affiliateDecrypte = $this->userService->getUncrypted($affiliate, 'email');
            dd($affiliateDecrypte);
            if ($affiliateDecrypte === $email) {
                return $affiliate;
            }
        }

        return null;
    }
}
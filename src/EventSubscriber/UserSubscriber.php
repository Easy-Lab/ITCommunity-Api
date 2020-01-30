<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\GeolocationService;
use App\Service\UserService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSubscriber implements EventSubscriber
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;
    protected $userService;
    protected $geolocationService;

    /**
     * UserSubscriber constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param UserService $userService
     */
    public function __construct(UserPasswordEncoderInterface $encoder, UserService $userService, GeolocationService $geolocationService)
    {
        $this->encoder = $encoder;
        $this->userService = $userService;
        $this->geolocationService = $geolocationService;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postPersist,
            Events::preUpdate,
            Events::postUpdate,
            Events::onFlush,
            Events::postLoad,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $user = $args->getEntity();


        if ($user instanceof User) {
            $this->encodePassword($user);
            $user->setHash(sha1((string)microtime(true)));
//            try {
//                $this->geolocationService->retrieveGeocode($user);
//            } catch (\Error $e) {
//            }
            $this->encryptFields($user);
            $user->setTosAcceptedAt(new \DateTime());


        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();
        if ($user instanceof User) {
            $this->decryptFields($user);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $user = $args->getEntity();

        if ($user instanceof User) {
            $this->encryptFields($user);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $user = $args->getEntity();

        if ($user instanceof User) {
            $this->encodePassword($user);
            $this->decryptFields($user);
        }
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        $classMetadata = $em->getClassMetadata(User::class);

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof User) {
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $user = $args->getEntity();
        if ($user instanceof User) {
            $this->decryptFields($user);
        }
    }

    /**
     * @param User $user
     */
    protected function encodePassword(User $user): void
    {
        $encoded = $this->encoder->encodePassword($user, $user->getPlainPassword());

        $user->setPassword($encoded);
    }

    private function encryptFields(User $user)
    {
        // Set the entity variables
        try {
            $this->userService->setCrypted($user, 'firstname', $user->getFirstname());
            $this->userService->setCrypted($user, 'lastname', $user->getLastname());
            $this->userService->setCrypted($user, 'email', $user->getEmail());
            $this->userService->setCrypted($user, 'address', $user->getAddress());
            if ($user->getAddress2()) {
                $this->userService->setCrypted($user, 'address2', $user->getAddress2());
            }
            $this->userService->setCrypted($user, 'zipcode', $user->getZipcode());
            $this->userService->setCrypted($user, 'city', $user->getCity());
            $this->userService->setCrypted($user, 'phone', $user->getPhone());
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse('Decode successful.', Response::HTTP_OK);
    }

    private function decryptFields(User $user)
    {
        // Decrypt the variables
        $firstname = $this->userService->getUncrypted($user, 'firstname');
        $lastname = $this->userService->getUncrypted($user, 'lastname');
        $email = $this->userService->getUncrypted($user, 'email');
        $address = $this->userService->getUncrypted($user, 'address');
        if ($user->getAddress2()) {
            $address2 = $this->userService->getUncrypted($user, 'address2');
        }
        $zipcode = $this->userService->getUncrypted($user, 'zipcode');
        $city = $this->userService->getUncrypted($user, 'city');
        $phone = $this->userService->getUncrypted($user, 'phone');

        // Set the entity variables
        try {
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $user->setAddress($address);
            $user->setAddress2($address2);
            $user->setZipcode($zipcode);
            $user->setCity($city);
            $user->setPhone($phone);
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse('Decode successful.', Response::HTTP_OK);
    }

}

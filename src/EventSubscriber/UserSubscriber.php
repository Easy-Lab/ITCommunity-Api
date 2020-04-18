<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\BugReport;
use App\Entity\Contact;
use App\Entity\ContactForm;
use App\Entity\Message;
use App\Entity\Picture;
use App\Entity\Review;
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
     * @param GeolocationService $geolocationService
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
        $subject = $args->getEntity();

        if ($subject instanceof User) {
            $this->encodePassword($subject);
            $subject->setHash(sha1((string)microtime(true)));
            $subject->setTosAcceptedAt(new \DateTime());
            $this->encryptFields($subject);
            $this->userService->setCrypted($subject, 'zipcode', $subject->getZipcode());
            $this->userService->setCrypted($subject, 'city', $subject->getCity());
            $this->userService->setCrypted($subject, 'phone', $subject->getPhone());
        }

        if ($subject instanceof Message) {
            $subject->setHash(sha1((string)microtime(true)));
        }

        if ($subject instanceof Contact) {
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
        }

        if ($subject instanceof Review) {
            $subject->setHash(sha1((string)microtime(true)));
        }

        if ($subject instanceof BugReport) {
            $subject->setHash(sha1((string)microtime(true)));
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
        }

        if ($subject instanceof Picture) {
            $subject->setHash(sha1((string)microtime(true)));
        }

        if ($subject instanceof ContactForm) {
            $subject->setHash(sha1((string)microtime(true)));
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $subject = $args->getEntity();

        if ($subject instanceof User) {
            $this->decryptFields($subject);
            $zipcode = $this->userService->getUncrypted($subject, 'zipcode');
            $city = $this->userService->getUncrypted($subject, 'city');
            $phone = $this->userService->getUncrypted($subject, 'phone');
            $subject->setZipcode($zipcode);
            $subject->setCity($city);
            $subject->setPhone($phone);
        }

        if ($subject instanceof Contact) {
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setEmail($email);
        }

        if ($subject instanceof BugReport) {
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setEmail($email);
        }

        if ($subject instanceof ContactForm) {
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setEmail($email);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function preUpdate(LifecycleEventArgs $args): void
    {
        $subject = $args->getEntity();

        if ($subject instanceof User) {
            $this->encryptFields($subject);
            $this->userService->setCrypted($subject, 'zipcode', $subject->getZipcode());
            $this->userService->setCrypted($subject, 'city', $subject->getCity());
            $this->userService->setCrypted($subject, 'phone', $subject->getPhone());
        }

        if ($subject instanceof Contact) {
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
        }

        if ($subject instanceof BugReport) {
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
        }

        if ($subject instanceof ContactForm) {
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $subject = $args->getEntity();

        if ($subject instanceof User) {
            $this->decryptFields($subject);
            $zipcode = $this->userService->getUncrypted($subject, 'zipcode');
            $city = $this->userService->getUncrypted($subject, 'city');
            $phone = $this->userService->getUncrypted($subject, 'phone');
            $subject->setZipcode($zipcode);
            $subject->setCity($city);
            $subject->setPhone($phone);
        }

        if ($subject instanceof Contact) {
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setEmail($email);
        }

        if ($subject instanceof BugReport) {
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setEmail($email);
        }

        if ($subject instanceof ContactForm) {
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setEmail($email);
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
            if ($entity instanceof Contact) {
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $subject = $args->getEntity();

        if ($subject instanceof User) {
            $this->decryptFields($subject);
            $zipcode = $this->userService->getUncrypted($subject, 'zipcode');
            $city = $this->userService->getUncrypted($subject, 'city');
            $phone = $this->userService->getUncrypted($subject, 'phone');
            $subject->setZipcode($zipcode);
            $subject->setCity($city);
            $subject->setPhone($phone);
        }

        if ($subject instanceof Contact) {
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setEmail($email);
        }

        if ($subject instanceof BugReport) {
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setEmail($email);
        }

        if ($subject instanceof ContactForm) {
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setEmail($email);
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

        // Set the entity variables
        try {
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $user->setAddress($address);
            $user->setAddress2($address2);
        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        return new JsonResponse('Decode successful.', Response::HTTP_OK);
    }

}

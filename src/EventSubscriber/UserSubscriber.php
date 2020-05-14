<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Affiliate;
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
            $this->userService->encodePassword($subject);
            $subject->setHash(sha1((string)microtime(true)));
            $subject->setTosAcceptedAt(new \DateTime());
            $this->userService->setCrypted($subject, 'firstname', $subject->getFirstname());
            $this->userService->setCrypted($subject, 'lastname', $subject->getLastname());
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
            $this->userService->setCrypted($subject, 'address', $subject->getAddress());
            if ($subject->getAddress2()) {
                $this->userService->setCrypted($subject, 'address2', $subject->getAddress2());
            }
            $this->userService->setCrypted($subject, 'zipcode', $subject->getZipcode());
            $this->userService->setCrypted($subject, 'city', $subject->getCity());
            $this->userService->setCrypted($subject, 'phone', $subject->getPhone());
        }

        if ($subject instanceof Message) {
            $subject->setHash(sha1((string)microtime(true)));
        }

        if ($subject instanceof Contact) {
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
            $subject->setHash(sha1((string)microtime(true)));
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

        if ($subject instanceof Affiliate) {
            $subject->setHash(sha1((string)microtime(true)));
            $this->userService->setCrypted($subject, 'firstname', $subject->getFirstname());
            $this->userService->setCrypted($subject, 'lastname', $subject->getLastname());
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $subject = $args->getEntity();

        if ($subject instanceof User) {
            $firstname = $this->userService->getUncrypted($subject, 'firstname');
            $lastname = $this->userService->getUncrypted($subject, 'lastname');
            $email = $this->userService->getUncrypted($subject, 'email');
            $address = $this->userService->getUncrypted($subject, 'address');
            if ($subject->getAddress2()) {
                $address2 = $this->userService->getUncrypted($subject, 'address2');
            }
            $zipcode = $this->userService->getUncrypted($subject, 'zipcode');
            $city = $this->userService->getUncrypted($subject, 'city');
            $phone = $this->userService->getUncrypted($subject, 'phone');
            $subject->setFirstname($firstname);
            $subject->setLastname($lastname);
            $subject->setEmail($email);
            $subject->setAddress($address);
            if ($subject->getAddress2()) {
                $subject->setAddress2($address2);
            }
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

        if ($subject instanceof Affiliate) {
            $firstname = $this->userService->getUncrypted($subject, 'firstname');
            $lastname = $this->userService->getUncrypted($subject, 'lastname');
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setFirstname($firstname);
            $subject->setLastname($lastname);
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
            $this->userService->setCrypted($subject, 'firstname', $subject->getFirstname());
            $this->userService->setCrypted($subject, 'lastname', $subject->getLastname());
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
            $this->userService->setCrypted($subject, 'address', $subject->getAddress());
            if ($subject->getAddress2()) {
                $this->userService->setCrypted($subject, 'address2', $subject->getAddress2());
            }
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

        if ($subject instanceof Affiliate) {
            $subject->setHash(sha1((string)microtime(true)));
            $this->userService->setCrypted($subject, 'firstname', $subject->getFirstname());
            $this->userService->setCrypted($subject, 'lastname', $subject->getLastname());
            $this->userService->setCrypted($subject, 'email', $subject->getEmail());
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws Exception
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $subject = $args->getEntity();

        if ($subject instanceof User) {
            $firstname = $this->userService->getUncrypted($subject, 'firstname');
            $lastname = $this->userService->getUncrypted($subject, 'lastname');
            $email = $this->userService->getUncrypted($subject, 'email');
            $address = $this->userService->getUncrypted($subject, 'address');
            if ($subject->getAddress2()) {
                $address2 = $this->userService->getUncrypted($subject, 'address2');
            }
            $zipcode = $this->userService->getUncrypted($subject, 'zipcode');
            $city = $this->userService->getUncrypted($subject, 'city');
            $phone = $this->userService->getUncrypted($subject, 'phone');
            $subject->setFirstname($firstname);
            $subject->setLastname($lastname);
            $subject->setEmail($email);
            $subject->setAddress($address);
            if ($subject->getAddress2()) {
                $subject->setAddress2($address2);
            }
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

        if ($subject instanceof Affiliate) {
            $firstname = $this->userService->getUncrypted($subject, 'firstname');
            $lastname = $this->userService->getUncrypted($subject, 'lastname');
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setFirstname($firstname);
            $subject->setLastname($lastname);
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
     * @throws Exception
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $subject = $args->getEntity();

        if ($subject instanceof User) {
            $firstname = $this->userService->getUncrypted($subject, 'firstname');
            $lastname = $this->userService->getUncrypted($subject, 'lastname');
            $email = $this->userService->getUncrypted($subject, 'email');
            $address = $this->userService->getUncrypted($subject, 'address');
            if ($subject->getAddress2()) {
                $address2 = $this->userService->getUncrypted($subject, 'address2');
            }
            $zipcode = $this->userService->getUncrypted($subject, 'zipcode');
            $city = $this->userService->getUncrypted($subject, 'city');
            $phone = $this->userService->getUncrypted($subject, 'phone');
            $subject->setFirstname($firstname);
            $subject->setLastname($lastname);
            $subject->setEmail($email);
            $subject->setAddress($address);
            if ($subject->getAddress2()) {
                $subject->setAddress2($address2);
            }
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

        if ($subject instanceof Affiliate) {
            $firstname = $this->userService->getUncrypted($subject, 'firstname');
            $lastname = $this->userService->getUncrypted($subject, 'lastname');
            $email = $this->userService->getUncrypted($subject, 'email');
            $subject->setFirstname($firstname);
            $subject->setLastname($lastname);
            $subject->setEmail($email);
        }
    }
}

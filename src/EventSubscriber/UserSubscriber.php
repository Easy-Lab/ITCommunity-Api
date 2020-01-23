<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSubscriber implements EventSubscriber
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;
    protected $userService;

    /**
     * UserSubscriber constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder, UserService $userService)
    {
        $this->encoder = $encoder;
        $this->userService = $userService;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postUpdate,
            Events::onFlush,
            Events::postLoad,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $user = $args->getEntity();

        if ($user instanceof User) {
            $this->encodePassword($user);
            $this->userService->setCrypted($user, 'firstname', $user->getFirstname());
            $this->userService->setCrypted($user, 'lastname', $user->getLastname());
            $this->userService->setCrypted($user, 'username', $user->getUsername());
            $this->userService->setCrypted($user, 'email', $user->getEmail());
            $this->userService->setCrypted($user, 'address', $user->getAddress());
            if ($user->getAddress2()) {
                $this->userService->setCrypted($user, 'address2', $user->getAddress2());
            }
            $this->userService->setCrypted($user, 'zipcode', $user->getZipcode());
            $this->userService->setCrypted($user, 'city', $user->getCity());
            $this->userService->setCrypted($user, 'phone', $user->getPhone());
            $user->setHash(sha1((string)microtime(true)));
            $user->setTosAcceptedAt(new \DateTime());
        }
    }

    public function postLoad(LifecycleEventArgs $args): void {
        $user = $args->getEntity();

        if ($user instanceof User) {
            $this->userService->getUncrypted($user, 'firstname');
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
                $entity->setUsername($entity->getEmail());
                $uow->recomputeSingleEntityChangeSet($classMetadata, $entity);
            }
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


}

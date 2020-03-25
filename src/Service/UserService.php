<?php

namespace App\Service;

use App\Entity\User;
use App\Utils\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    protected $em;
    protected $encoder;
    protected $features;
    protected $container;
    protected $security;

    /** @var  TokenStorageInterface */
    private $tokenStorage;

    public function __construct(ContainerInterface $container, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Security $security, TokenStorageInterface $storage)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->container = $container;
        $this->security = $security;
        $this->tokenStorage = $storage;
    }

    public function getUncrypted($user, $column)
    {
        $value = $user->{'get'.ucfirst($column)}();
        if(is_null($value)) return $value;
        $value = $this->security->decrypt($value);
        if(!is_null($value) && in_array($column, ['birthdate'])) {
            $date = new \DateTime();
            $date->setTimestamp(strtotime($value));
            return $date;
        }
        return $value;
    }

    public function setCrypted(&$user, $column, $value)
    {
        if(method_exists($user, 'set'.ucfirst($column).'Hash')) {
            $user->{'set'.ucfirst($column).'Hash'}($value === null ? null : $this->raw2hash($value));
        }
        $value = $value === null ? null : $this->security->encrypt($value);
        $user->{'set'.ucfirst($column)}($value);
    }

    public function raw2hash($email)
    {
        return hash('sha1', $email . $this->container->getParameter('secret'));
    }

    public function getCurrentUser()
    {
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {

            /** @var User $user */
            $user = $token->getUser();
            return $user;

        } else {
            return null;
        }
    }
}

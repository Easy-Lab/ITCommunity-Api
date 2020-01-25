<?php

namespace App\Service;


use App\Utils\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class UserService
{
    protected $translator;
    protected $em;
    protected $encoder;
    protected $features;
    protected $mailService;
    protected $pictureService;
    protected $container;
    protected $security;

    public function __construct(TranslatorInterface $translator, ContainerInterface $container, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, Security $security)
    {
        $this->translator = $translator;
        $this->em = $em;
        $this->encoder = $encoder;
        $this->container = $container;
        $this->security = $security;
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
}

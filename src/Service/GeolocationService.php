<?php


namespace App\Service;

use App\Entity\User;
use App\Utils\Mapping;
use Doctrine\ORM\EntityManagerInterface;

class GeolocationService
{
    protected $mapping;
    protected $em;
    /**
     * GeolocationService constructor.
     */
    public function __construct(Mapping $mapping,EntityManagerInterface $em)
    {
        $this->mapping = $mapping;
        $this->em = $em;
    }

    public function getManager(): EntityManagerInterface
    {
        return $this->em;
    }

    public function retrieveGeocode(User $user)
    {
        list($latitude, $longitude) = $this->mapping->retrieveCoordinates($user);
        $return = false;
        if (!is_null($latitude) && !is_null($longitude)) {

            $randomLatOffset = (((rand(0, 200) - 100) / 100) + 1) * (48.4508751 - 48.4521836);
            $randomLngOffset = (((rand(0, 200) - 100) / 100) + 1) * (2.7655379 - 2.7668559);

            if ($randomLatOffset != 0) $randomLatOffset /= 2;
            if ($randomLngOffset != 0) $randomLngOffset /= 2;

            $user->setLatitude($latitude + $randomLatOffset);
            $user->setLongitude($longitude + $randomLngOffset);
            $this->em->persist($user);
            $this->em->flush();

            //$mailService->sendRegisterDoneMail($user);
            $return = true;
        }

        return $return;
    }
}

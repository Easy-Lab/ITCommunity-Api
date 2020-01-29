<?php

namespace App\Utils;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Mapping
{
    protected $container;
    protected $userService;

    public function __construct(ContainerInterface $container, UserService $userService)
    {
        $this->container = $container;
        $this->userService = $userService;
    }

    public function retrieveCoordinates(User $user)
    {
        $address = $user->getAddress();
        $address .= ' ' . $user->getZipcode();
        $address .= ' ' . $user->getCity();
        $address = trim(preg_replace('/\s+/', ' ', $address));
        if (empty($address)) return [null, null];

        $apiKey = getenv('GOOGLE_GEOCODING_API_KEY');
        $queryParameters = [
            'address' => $address,
            'key' => $apiKey
        ];
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($queryParameters);
        $options = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false
            ]
        ];
        $response = file_get_contents($url, false, stream_context_create($options));

        if ($response) {
            $obj = json_decode($response);
            if ($obj->status === "OK") {
                $location = $obj->results[0]->geometry->location;
                return [$location->lat, $location->lng];
            }
        }

        return [null, null];
    }
}

<?php


namespace App\Event\Listener;

use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    /**
     *
     * @param JWTCreatedEvent $event
     *
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        $payload['username'] = $this->userService->getUncrypted($user, 'username');
        $payload['password'] = $user->getPassword();

        $event->setData($payload);
    }

}
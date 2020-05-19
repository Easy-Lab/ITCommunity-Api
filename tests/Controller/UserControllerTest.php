<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends AbstractWebTestCase
{
    /**
     * @var int
     */
    protected static $username;
    protected static $email;
    protected static $firstname;
    protected static $lastname;
    protected static $address;
    protected static $city;
    protected static $zipcode;
    protected static $phone;
    protected static $step;
    protected static $informations_enabled;
    protected static $password;
    protected static $hash;

    public function testCreateAction()
    {
        self::$username = $this->faker->userName;
        self::$email = $this->faker->email;
        self::$firstname = $this->faker->firstName;
        self::$lastname = $this->faker->lastName;
        self::$address = $this->faker->streetAddress;
        self::$city = $this->faker->city;
        self::$zipcode = $this->faker->postcode;
        self::$phone = $this->faker->phoneNumber;
        self::$step = $this->faker->numberBetween(1,3);
        self::$informations_enabled = $this->faker->boolean(50);
        self::$password = $this->faker->password(6);

        $this->client->request(
            Request::METHOD_POST,
            '/users',
            [],
            [],
            [],
            json_encode([
                'firstname' => self::$firstname,
                'lastname' => self::$lastname,
                'email' => self::$email,
                'username' => self::$username,
                'address' => self::$address,
                'city' => self::$city,
                'zipcode' => self::$zipcode,
                'phone' => self::$phone,
                'step' => self::$step,
                'informations_enabled' => self::$informations_enabled,
                'plainPassword' => self::$password
            ])
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('username', $responseContent);

        self::$username = $responseContent['username'];
    }

    public function testUnauthorizedUpdateAction()
    {
        self::$email = $this->faker->email;

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/users/%s', self::$username),
            [],
            [],
            [],
            json_encode([
                'email' => self::$email,
            ])
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testUpdateAction()
    {
        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/users/%s', self::$username),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'step' => 2,
            ])
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testNotFoundUpdateAction()
    {
        $this->client->request(
            Request::METHOD_PATCH,
            '/users/username',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'email' => self::$email,
            ])
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testListAction()
    {
        $this->client->request(Request::METHOD_GET, '/users');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertContains('users', $this->client->getResponse()->getContent());
    }

    public function testFilterListAction()
    {
        $this->client->request(Request::METHOD_GET, sprintf('/users?user_filter[username]=%s', self::$username));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertContains('users', $this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('username', array_search(self::$username, $responseContent['users'][0]));
    }

    public function testShowAction()
    {
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s', self::$username));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('username', array_search(self::$username, $responseContent));
    }

    public function testReviewsShowAction()
    {
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s/reviews', self::$username));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testMessagesShowAction()
    {
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s/messages', self::$username));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testEvaluationsShowAction()
    {
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s/evaluations', self::$username));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testPicturesShowAction()
    {
        $this->client->request(Request::METHOD_GET, sprintf('/users/%s/pictures', self::$username));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testAuthenticatedShowAction()
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf('/users/%s', self::$username),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('username', array_search(self::$username, $responseContent));

        $this->assertArrayHasKey('hash', $responseContent);

        self::$hash = $responseContent['hash'];
    }

    public function testPasswordUpdateAction()
    {
        self::$password = $this->faker->password(6);

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/users/forgot_password/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'plainPassword' => self::$password,
            ])
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testNotFoundPasswordUpdateAction()
    {
        $this->client->request(
            Request::METHOD_PATCH,
            '/users/forgot_password/user',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'plainPassword' => self::$password,
            ])
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testNotFoundShowAction()
    {
        $this->client->request(Request::METHOD_GET, '/users/username');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testNotFoundReviewsShowAction()
    {
        $this->client->request(Request::METHOD_GET, '/users/user/reviews');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testNotFoundMessagesShowAction()
    {
        $this->client->request(Request::METHOD_GET, '/users/user/messages');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testNotFoundEvaluationsShowAction()
    {
        $this->client->request(Request::METHOD_GET, '/users/user/evaluations');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testNotFoundPicturesShowAction()
    {
        $this->client->request(Request::METHOD_GET, '/users/user/pictures');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testUnauthorizedDeleteAction()
    {
        $this->client->request(Request::METHOD_DELETE, sprintf('/users/%s', self::$username));

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testDeleteAction()
    {
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf('/users/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testNotFoundDeleteAction()
    {
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf('/users/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token]
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }
}

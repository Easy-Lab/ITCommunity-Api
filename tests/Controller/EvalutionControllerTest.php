<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EvalutionControllerTest extends AbstractWebTestCase
{
    protected static $username;
    protected static $emailContact;
    protected static $emailUser;
    protected static $firstname;
    protected static $lastname;
    protected static $address;
    protected static $city;
    protected static $zipcode;
    protected static $phone;
    protected static $step;
    protected static $informations_enabled;
    protected static $type;
    protected static $question;
    protected static $rating;
    protected static $feedback;
    protected static $hash;

    public function testContactCreateAction()
    {
        self::$emailContact = $this->faker->email;
        self::$firstname = $this->faker->firstName;
        self::$lastname = $this->faker->lastName;

        $this->client->request(
            Request::METHOD_POST,
            '/contacts',
            [],
            [],
            [],
            json_encode([
                'firstname' => self::$firstname,
                'lastname' => self::$lastname,
                'email' => self::$emailContact,
            ])
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testUserCreateAction()
    {
        self::$username = $this->faker->userName;
        self::$emailUser = $this->faker->email;
        self::$firstname = $this->faker->firstName;
        self::$lastname = $this->faker->lastName;
        self::$address = $this->faker->streetAddress;
        self::$city = $this->faker->city;
        self::$zipcode = $this->faker->postcode;
        self::$phone = $this->faker->phoneNumber;
        self::$step = $this->faker->numberBetween(1, 3);
        self::$informations_enabled = $this->faker->boolean(50);

        $this->client->request(
            Request::METHOD_POST,
            '/users',
            [],
            [],
            [],
            json_encode([
                'firstname' => self::$firstname,
                'lastname' => self::$lastname,
                'email' => self::$emailUser,
                'username' => self::$username,
                'address' => self::$address,
                'city' => self::$city,
                'zipcode' => self::$zipcode,
                'phone' => self::$phone,
                'step' => self::$step,
                'informations_enabled' => self::$informations_enabled,
                'plainPassword' => 'test',
            ])
        );

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );
    }

    public function testMessageCreateAction()
    {
        self::$question = $this->faker->paragraphs(1, 2);

        $this->client->request(
            Request::METHOD_POST,
            '/messages',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode([
                'email' => self::$emailContact,
                'username' => self::$username,
                'question' => self::$question
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

        $this->assertArrayHasKey('hash', $responseContent);

        self::$hash = $responseContent['hash'];
    }

    public function testCreateAction()
    {
        self::$rating = $this->faker->numberBetween(1, 5);
        self::$feedback = $this->faker->paragraphs(1, 2);

        $this->client->request(
            Request::METHOD_POST,
            '/evaluations',
            [],
            [],
            [],
            json_encode([
                'hash' => self::$hash,
                'rating' => self::$rating,
                'feedback' => self::$feedback
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

        $this->assertArrayHasKey('hash', $responseContent);

        self::$hash = $responseContent['hash'];
    }

    public function testUnauthorizedUpdateAction()
    {
        self::$feedback = $this->faker->paragraphs(1, 2);

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/evaluations/%s', self::$hash),
            [],
            [],
            [],
            json_encode([
                'feedback' => self::$feedback
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
        self::$feedback = $this->faker->paragraphs(1, 2);

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/evaluations/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode([
                'feedback' => self::$feedback
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
        self::$feedback = $this->faker->paragraphs(1, 2);

        $this->client->request(
            Request::METHOD_PATCH,
            '/evaluations/evaluation',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token],
            json_encode([
                'feedback' => self::$feedback
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
        $this->client->request(Request::METHOD_GET, '/evaluations');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertContains('evaluations', $this->client->getResponse()->getContent());
    }

    public function testFilterListAction()
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf('/evaluations?evaluation_filter[hash]=%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertContains('evaluations', $this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('hash', array_search(self::$hash, $responseContent['evaluations'][0]), true);
    }

    public function testShowAction()
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf('/evaluations/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('hash', array_search(self::$hash, $responseContent, true));
    }

    public function testNotFoundShowAction()
    {
        $this->client->request(Request::METHOD_GET, '/evaluations/evaluation');

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
        $this->client->request(Request::METHOD_DELETE, sprintf('/evaluations/%s', self::$hash));

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
            sprintf('/evaluations/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
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
            sprintf('/evaluations/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->token]
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
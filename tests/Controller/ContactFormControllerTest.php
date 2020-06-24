<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ContactFormControllerTest extends AbstractWebTestCase
{
    protected static $firstname;
    protected static $lastname;
    protected static $phone;
    protected static $email;
    protected static $body;
    protected static $hash;

    public function testAuthenticatedCreateAction()
    {
        self::$firstname = $this->faker->firstName;
        self::$lastname = $this->faker->lastName;
        self::$phone = $this->faker->phoneNumber;
        self::$email = $this->faker->email;
        self::$body = $this->faker->paragraphs(1,2);

        $this->client->request(
            Request::METHOD_POST,
            '/contactforms',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'firstname' => self::$firstname,
                'lastname' => self::$lastname,
                'phone' => self::$phone,
                'email' => self::$email,
                'subject' => self::$body,
                'body' => self::$body
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

    public function testUpdateAction()
    {
        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/contactforms/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'subject' => 'Updated',
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

    public function testUnauthorizedUpdateAction()
    {
        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/contactforms/%s', self::$hash),
            [],
            [],
            [],
            json_encode([
                'subject' => 'Updated',
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

    public function testListAction()
    {
        $this->client->request(Request::METHOD_GET, '/contactforms');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertContains('contactforms', $this->client->getResponse()->getContent());
    }

    public function testShowAction()
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf('/contactforms/%s', self::$hash),
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

        $this->assertSame('hash', array_search(self::$hash, $responseContent, true));
    }

    public function testNotFoundShowAction()
    {
        $this->client->request(Request::METHOD_GET, '/contactforms/contactform');

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
        $this->client->request(Request::METHOD_DELETE, sprintf('/contactforms/%s', self::$hash));

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
            sprintf('/contactforms/%s', self::$hash),
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
            sprintf('/contactforms/%s', self::$hash),
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
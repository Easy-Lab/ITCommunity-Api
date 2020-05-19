<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BugReportControllerTest extends AbstractWebTestCase
{
    protected static $hash;
    protected static $email;
    protected static $firstname;
    protected static $lastname;
    protected static $body;

    public function testCreateAction()
    {
        self::$email = $this->faker->email;
        self::$firstname = $this->faker->firstName;
        self::$lastname = $this->faker->lastName;
        self::$body = $this->faker->paragraphs(1,2);

        $this->client->request(
            Request::METHOD_POST,
            '/bugreports',
            [],
            [],
            [],
            json_encode([
                'firstname' => self::$firstname,
                'lastname' => self::$lastname,
                'email' => self::$email,
                'subject' => self::$body,
                'body' => self::$body,
                'solved' => true
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
        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/bugreports/status/%s', self::$hash),
            [],
            [],
            [],
            json_encode([
                'solved' => true,
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
            sprintf('/bugreports/status/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'solved' => true,
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
            '/bugreports/status/bug',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'solved' => true,
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
        $this->client->request(Request::METHOD_GET, '/bugreports');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertContains('bugreports', $this->client->getResponse()->getContent());
    }

    public function testShowAction()
    {
        $this->client->request(Request::METHOD_GET, sprintf('/bugreports/%s', self::$hash));

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
        $this->client->request(Request::METHOD_GET, '/bugreports/bugreport');

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
        $this->client->request(Request::METHOD_DELETE, sprintf('/bugreports/%s', self::$hash));

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
            sprintf('/bugreports/%s', self::$hash),
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
            '/bugreports/bugreport',
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
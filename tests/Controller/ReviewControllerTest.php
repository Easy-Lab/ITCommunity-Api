<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewControllerTest extends AbstractWebTestCase
{
    protected static $body;
    protected static $rating;
    protected static $type;
    protected static $name_component;
    protected static $company_component;
    protected static $other_information_component;
    protected static $hash;

    public function testUnauthorizedCreateAction()
    {
        self::$body = $this->faker->paragraphs(1,2);
        self::$rating = $this->faker->numberBetween(1,5);
        self::$type = $this->faker->randomElement(['CPU','GPU']);
        self::$name_component = $this->faker->paragraphs(1,2);
        self::$company_component = $this->faker->paragraphs(1,2);
        self::$other_information_component = $this->faker->paragraphs(1,2);


        $this->client->request(
            Request::METHOD_POST,
            '/reviews',
            [],
            [],
            [],
            json_encode([
                'body' => self::$body,
                'rating' => self::$rating,
                'type' => self::$type,
                'name_component' => self::$name_component,
                'company_component' => self::$company_component,
                'other_information_component' => self::$other_information_component
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

    public function testCreateAction()
    {
        self::$rating = 5;

        $this->client->request(
            Request::METHOD_POST,
            '/reviews',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'body' => 'Ut accusantium ad facere qui est. Voluptas quae rerum voluptas perspiciatis molestiae voluptas assumenda. Nobis impedit laudantium eaque saepe quae.',
                'rating' => self::$rating,
                'type' => 'Gpu',
                'name_component' => 'Component',
                'company_component' => 'Company',
                'other_information_component' => 'Other'
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
        self::$rating = 4;

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/reviews/%s', self::$hash),
            [],
            [],
            [],
            json_encode([
                'rating' => self::$rating,
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
            sprintf('/reviews/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'rating' => self::$rating,
            ])
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('rating', array_search(self::$rating, $responseContent));
    }

    public function testNotFoundUpdateAction()
    {
        $this->client->request(
            Request::METHOD_PATCH,
            '/reviews/reviews',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'rating' => 4,
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
        $this->client->request(Request::METHOD_GET, '/reviews');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertContains('reviews', $this->client->getResponse()->getContent());
    }

    public function testFilterListAction()
    {
        $this->client->request(Request::METHOD_GET, sprintf('/reviews?review_filter[rating]=%s', self::$rating));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $this->assertContains('reviews', $this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('rating', array_search(self::$rating, $responseContent['reviews'][0]));
    }

    public function testShowAction()
    {
        $this->client->request(
            Request::METHOD_PATCH,
            sprintf('/reviews/%s', self::$hash),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$this->token],
            json_encode([
                'rating' => self::$rating,
            ])
        );

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Content-Type',
                'application/json'
            )
        );

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame('hash', array_search(self::$hash, $responseContent));
    }

    public function testNotFoundShowAction()
    {
        $this->client->request(Request::METHOD_GET, '/reviews/review');

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
        $this->client->request(Request::METHOD_DELETE, sprintf('/reviews/%s', self::$hash));

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
            sprintf('/reviews/%s', self::$hash),
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
            '/reviews/review',
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

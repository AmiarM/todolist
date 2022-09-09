<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    private $client;

    public function setUp(): void
    {
        //parent::setUp();
        $this->client = static::createClient();
    }

    public function testHomePage()
    {
        $this->client->request('GET', '/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function test404WhenFakeLink()
    {
        // Assert that not existing route return 404
        $this->client->request('GET', '/-1');
        static::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}

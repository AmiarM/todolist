<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase

{
    use FixturesTrait;
    use NeedLogin;
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testAuthPageIsRestricted()
    {
        $this->client->request('GET', '/users');
        $this->assertResponseRedirects('/login_check');
    }
    public function loginWithAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login_check');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'email' => 'christiane56@hotmail.fr',
            'password' => 'password'
        ]);

        $this->client->submit($form);
    }
    public function testList(): void
    {
        $this->client->request('GET', '/users');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/users');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Liste des utilisateurs', $crawler->filter('h1')->text());
        self::assertStringContainsString('Edit', $crawler->filter('a.btn.btn-success')->text());
    }
}

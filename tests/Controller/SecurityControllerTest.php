<?php

namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testLogin(): void
    {

        $crawler = $this->client->request('GET', '/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('input[name="username"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="password"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="_csrf_token"]')->count());

        $form = $crawler->selectButton('Se connecter')->form();

        $form['username'] = 'admin';
        $form['password'] = 'root';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame('Se déconnecter', $crawler->filter('a.pull-right.btn.btn-danger')->text());
    }

    public function testLoginUsernameDontExist(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();

        $form['username'] = 'boby';
        $form['password'] = '';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame('L\'utilsateur n\'existe pas.', $crawler->filter('div.alert-danger')->text());
    }

    public function testLoginFailPassword(): void
    {

        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form();

        $form['username'] = 'admin';
        $form['password'] = 'azertyui';
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame('Mot de passe incorrect', $crawler->filter('div.alert-danger')->text());
    }

    public function tearDown(): void
    {
        $this->client = null;
        $crawler = null;
    }

}
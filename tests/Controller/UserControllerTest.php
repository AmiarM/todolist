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
            'email' => 'charrier.lucas@labbe.com',
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

    public function testUserCreatePageAccess()
    {
        $this->loginWithAdmin();
        $crawler = $this->client->request('GET', '/users/create');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    //Edit scenarii
    public function testUserEditPageAccess()
    {
        $this->loginWithAdmin();
        $crawler = $this->client->request('GET', '/users/2/edit');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testTaskDelete()
    {
        $this->loginWithAdmin();
        $this->client->request('GET', '/tasks/13/delete');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }


    public function testUserEditPageError404()
    {
        $this->loginWithAdmin();
        $crawler = $this->client->request('GET', '/users/99999/edit');
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testUserCreation()
    {
        $this->loginWithAdmin();

        $crawler = $this->client->request('POST', '/users/create');
        $this->client->submitForm('Ajouter', [
            'user[username]' => 'toto',
            'user[password][first]' => 'toto',
            'user[password][second]' => 'toto',
            'user[email]' => 'toto@gmail.com',
        ]);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    public function testUserEdit()
    {
        $this->loginWithAdmin();
        $crawler = $this->client->request('GET', '/users/3/edit');
        $form = $crawler->selectButton('modifier')->form([
            'user[username]' => "Deuf",
            'user[email]' => "toto@gmail.com",
            'user[password][first]' => "john",
            'user[password][second]' => "john",
            //'user[roles]' => "ROLE_ADMIN"
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/users');
        //$this->client->followRedirect();
        $this->assertSelectorExists('.alert-success');
    }
}

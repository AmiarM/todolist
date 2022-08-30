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
    public function testAuthPageIsRestricted()
    {
        $client = static::createClient();
        $client->request('GET', '/users');
        $this->assertResponseRedirects('/login_check');
    }
    public function testLetAuthencatedUserAccess()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        $this->login($client, $users['user_user']);
        $client->request('GET', '/tasks');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    public function testAdminRequireAdminRole()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/users.yaml']);
        $this->login($client, $users['user_admin']);
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


    public function loginWithAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login_check');

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'email' => 'admin@admin.com',
            'password' => 'password'
        ]);

        $this->client->submit($form);
    }
}

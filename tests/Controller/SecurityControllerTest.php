<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;



class SecurityControllerTest extends  WebTestCase
{
    use FixturesTrait;
    public function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }
    public function testLoginWithBadCredentials()
    {
        $crawler = $this->client->request('GET', '/login_check');
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'test@test.fr',
            'password' => 'fakepassword'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/login_check');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }
    public function testLogout()
    {
        $this->client->followRedirects();
        $this->client->request('GET', '/logout');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}

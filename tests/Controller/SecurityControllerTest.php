<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;



class SecurityControllerTest extends  WebTestCase
{
    use FixturesTrait;
    public function testLoginWithBadCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login_check');
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'test@test.fr',
            'password' => 'fakepassword'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login_check');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }
}

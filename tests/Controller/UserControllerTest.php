<?php


namespace App\Tests\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    private $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function loginWithAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, ['username' => 'admin', 'password' => 'root']);
    }

    public function testListAction(): void
    {
        $this->loginWithAdmin();

        $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateAction(): void
    {
        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame('Nom d\'utilisateur', $crawler->filter('label[for="user_username"]')->text());
        $this->assertSame('Mot de passe', $crawler->filter('label[for="user_password_first"]')->text());
        $this->assertSame('Adresse email', $crawler->filter('label[for="user_email"]')->text());

        $this->assertEquals(1, $crawler->filter('input[name="user[username]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][first]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][second]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[email]"]')->count());
        $this->assertEquals(2, $crawler->filter('input[name="user[roles][]"]')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'boby';
        $form['user[password][first]'] = 'azerty';
        $form['user[password][second]'] = 'azerty';
        $form['user[email]'] = 'newUser@example.org';
        $form['user[roles][0]']->tick();
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());


    }

    public function testEditeAction(): void
    {
        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/users/4/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame('Nom d\'utilisateur', $crawler->filter('label[for="user_username"]')->text());
        $this->assertSame('Mot de passe', $crawler->filter('label[for="user_password_first"]')->text());
        $this->assertSame('Adresse email', $crawler->filter('label[for="user_email"]')->text());

        $this->assertEquals(1, $crawler->filter('input[name="user[username]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][first]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[password][second]"]')->count());
        $this->assertEquals(1, $crawler->filter('input[name="user[email]"]')->count());
        $this->assertEquals(2, $crawler->filter('input[name="user[roles][]"]')->count());

        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'bobynight';
        $form['user[password][first]'] = 'root';
        $form['user[password][second]'] = 'root';
        $form['user[email]'] = 'newUser@example.org';
        $form['user[roles][0]']->tick();
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

    }


}
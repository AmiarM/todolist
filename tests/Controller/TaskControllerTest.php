<?php


namespace App\Tests\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class TaskControllerTest extends WebTestCase
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

    public function loginWithUser(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, ['username' => 'user', 'password' => 'root']);
    }

    public function testListAction(): void
    {
        $this->loginWithUser();
        $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateAction(): void
    {
        $this->loginWithUser();

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame('Title', $crawler->filter('label[for="task_title"]')->text());
        $this->assertEquals(1, $crawler->filter('input[name="task[title]"]')->count());
        $this->assertSame('Content', $crawler->filter('label[for="task_content"]')->text());
        $this->assertEquals(1, $crawler->filter('textarea[name="task[content]"]')->count());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'Test Super titre de tache';
        $form['task[content]'] = 'Test Contenu de la supertache blablabla.';
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

    }

    public function testEditAction(): void
    {
        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks/' . random_int(1, 6) . '/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSame('Title', $crawler->filter('label[for="task_title"]')->text());
        $this->assertEquals(1, $crawler->filter('input[name="task[title]"]')->count());
        $this->assertSame('Content', $crawler->filter('label[for="task_content"]')->text());
        $this->assertEquals(1, $crawler->filter('textarea[name="task[content]"]')->count());

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'Test modification du Super titre de tache';
        $form['task[content]'] = 'Test modification du contenu de la supertache blablabla.';
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

    }

    public function testToggleTaskAction(): void
    {
        $this->loginWithUser();

        $this->client->request('GET', '/tasks/5/toggle');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

    }

    public function testDeleteTaskAction(): void
    {
        $this->loginWithUser();

        $this->client->request('GET', '/tasks/21/delete');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());

    }

    public function tearDown(): void
    {
        $this->client = null;
        $crawler = null;
        $form = null;
    }
}
<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use App\Tests\NeedLogin;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase

{
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function testAuthPageIsRestricted()
    {
        $this->client->request('GET', '/tasks');
        $this->assertResponseRedirects('/login_check');
    }

    //------------------------------------------------------------------------
    public function testListAction()
    {
        $this->loginWithUser();
        $this->client->request('GET', '/tasks');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    //------------------------------------------------------------------------
    public function testListEnblingTrueAction()
    {
        $this->loginWithUser();
        $this->client->request('GET', '/tasks/Enabling/true');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
    //-------------------------------------------------------------------------
    public function testCreateAction()
    {
        $this->loginWithUser();

        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'title';
        $form['task[content]'] = 'content';
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }
    //-------------------------------------------------------------------------
    public function testModifyAction()
    {
        $this->loginWithUser();

        $crawler = $this->client->request('GET', '/tasks/123/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'etttt';
        $form['task[content]'] = 'Praesentium voluptatibus dolor officia. Hic error quibusdam minima officiis consectetur. Dolores quae voluptatem blanditiis quo.';
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }
    //-------------------------------------------------------------------------
    public function testToggleTaskAction(): void
    {
        $this->loginWithUser();

        $this->client->request('GET', '/tasks/123/toggle');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.alert-success')->count());
    }
    //-------------------------------------------------------------------------
    public function loginWithUser(): void
    {
        $crawler = $this->client->request('GET', '/login_check');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'email' => 'benjamin12@laposte.net',
            'password' => 'password'
        ]);

        $this->client->submit($form);
    }
    public function testTaskDeletePageError404()
    {
        $this->loginWithUser();
        $crawler = $this->client->request('GET', '/tasks/9999999/delete');
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}

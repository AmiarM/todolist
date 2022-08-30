<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{

    /** @var TaskRepository */
    protected $taskRepository;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = self::createClient();
        $this->taskRepository = self::getContainer(TaskRepository::class);
    }
    public function testAuthPageIsRestricted()
    {
        $this->client = static::createClient();
        $this->client->request('GET', '/tasks');
        $this->assertResponseRedirects('/login_check');
    }

    public function loginWithAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login_check');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'email' => 'user@user.com',
            'password' => 'password'
        ]);
        $this->client->submit($form);
    }
    public function testList(): void
    {
        $this->client->request('GET', '/tasks');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->loginWithAdmin();
        $crawler = $this->client->request('GET', '/tasks');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer une tâche', $crawler->filter('a.btn.btn-info.pull-right')->text());
    }
    public function testListActionFinished()
    {
        $this->client->request('GET', '/Enabling/true');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->loginWithAdmin();
        $crawler = $this->client->request('GET', '/Enabling/true');
        $link = $crawler->selectLink('Accéder à la liste des tâches terminés')->link();
        $crawler = $this->client->click($link);
        $this->assertSame("Liste des tâches terminées", $crawler->filter('h2')->text());
    }

    public function testCreate(): void
    {
        $this->client->request('GET', '/tasks/create');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->loginWithAdmin();

        $crawler = $this->client->request('GET', '/tasks/create');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertCount(3, $crawler->filter('input'));
        self::assertEquals('Ajouter', $crawler->filter('button.btn.btn-success')->text());

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'Titre de la tâche 2',
            'task[content]' => 'Description de la tâche 2'
        ]);

        $this->client->submit($form);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('task_list', $this->client->getRequest()->get('_route'));
        self::assertEquals(
            'La tâche a été bien été ajoutée.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche 2']);
        self::assertInstanceOf(Task::class, $task);
        self::assertEquals('Titre de la tâche 2', $task->getTitle());
        self::assertEquals('Description de la tâche 2', $task->getContent());
        self::assertEquals('nixe', $task->getUser()->getUsername());
        self::assertEquals('nixedu06@gmail.com', $task->getUser()->getEmail());
    }

    public function testEdit(): void
    {
        $this->client->request('GET', '/tasks/2/edit');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->loginWithAdmin();
        $crawler = $this->client->request('GET', '/tasks/2/edit');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertCount(3, $crawler->filter('input'));
        self::assertEquals('Modifier', $crawler->filter('button.btn.btn-success.pull-right')->text());
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'Titre de la tâche',
            'task[content]' => 'Description de la tâche'
        ]);
        $this->client->submit($form);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('task_list', $this->client->getRequest()->get('_route'));
        self::assertEquals(
            'La tâche a bien été modifiée.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );
        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche']);
        self::assertInstanceOf(Task::class, $task);
        self::assertEquals('Titre de la tâche', $task->getTitle());
        self::assertEquals('Description de la tâche', $task->getContent());
        self::assertEquals('nixe', $task->getUser()->getUsername());
        self::assertEquals('nixedu06@gmail.com', $task->getUser()->getEmail());
    }
    public function testToggle(): void
    {
        $this->client->request('GET', '/tasks/2/toggle');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->loginWithAdmin();
        $this->client->request('GET', '/tasks/2/toggle');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('task_list', $this->client->getRequest()->get('_route'));
        self::assertEquals(
            'La tâche Titre de la tâche a bien été marquée comme faite.',
            $crawler->filter('div.alert.alert-success')->text(null, true)
        );

        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche']);
        self::assertInstanceOf(Task::class, $task);
        self::assertEquals('2', $task->getIsDone());
    }
    public function testDelete(): void
    {
        $this->client->request('DELETE', '/tasks/2/delete');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->loginWithAdmin();
        $this->client->request('DELETE', '/tasks/2/delete');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('task_list', $this->client->getRequest()->get('_route'));
        self::assertEquals('La tâche a bien été supprimée.', $crawler->filter('div.alert.alert-danger')->text(null, true));
        $task = $this->taskRepository->findOneBy(['title' => 'Titre de la tâche']);
        self::assertEmpty($task);
    }
}

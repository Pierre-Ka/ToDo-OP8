<?php

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Controller\AbstractControllerTest;

class CreateTaskControllerTest extends AbstractControllerTest
{
    /** @var TaskRepository */
    protected $taskRepository;
    /** @var UserRepository */
    protected $userRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taskRepository = $this->getContainer()->get(TaskRepository::class);
        $this->userRepository = $this->getContainer()->get(UserRepository::class);
    }

    /************************************* Test Create Success **********************************************/

    public function testCreateTaskWithValidData()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/create');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'New Task for create toggle and delete test',
            'task[content]' => 'New content for new task'
        ]);
        $this->client->submit($form);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! La tâche a été bien été ajoutée.', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', [$crawler->filter('h1')->text()]);

        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
        self::assertInstanceOf(Task::class, $testTask);
        self::assertEquals('New Task for create toggle and delete test', $testTask->getTitle());
        self::assertEquals('New content for new task', $testTask->getContent());
    }

    /************************************* Test Create Fail **********************************************/

    public function testCreateTaskWithEmptyTitle()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/create');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => '',
            'task[content]' => 'New content for new task'
        ]);
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer une nouvelle tache', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->taskRepository->findOneBy(['title' => '']));
    }

    public function testCreateTaskWithEmptyContent()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/create');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'New Task for empty content test',
            'task[content]' => ''
        ]);
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer une nouvelle tache', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->taskRepository->findOneBy(['title' => 'New Task for empty content test']));
    }
}

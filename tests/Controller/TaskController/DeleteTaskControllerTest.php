<?php

namespace App\Tests\Controller\TaskController;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Controller\AbstractControllerTest;

class DeleteTaskControllerTest extends AbstractControllerTest
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

    /************************************* Test Delete Fail **********************************************/

    public function testDeleteTaskByNonUser()
    {
        $crawler = $this->client->request('GET', '/tasks/11/delete');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testDeleteTaskByAdminAsNonAuthor()
    {
        // Setter un user
        $task = $this->taskRepository->findOneBy(['id' => 33]);
        $user = $this->userRepository->findOneBy(['username' => 'user']);
        $task->setUser($user);

        // Recuperer un admin
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/tasks/33/delete');
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        self::assertContains('Access Denied. (403 Forbidden)', [$crawler->filter('title')->text()]);
    }

    public function testDeleteTaskByUserAsNonAuthor()
    {
        // Setter un admin
        $task = $this->taskRepository->findOneBy(['id' => 33]);
        $user = $this->userRepository->findOneBy(['username' => 'admin']);
        $task->setUser($user);

        // Recuperer un user
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/33/delete');
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        self::assertContains('Access Denied. (403 Forbidden)', [$crawler->filter('title')->text()]);
    }

    public function testDeleteTaskByUserOnAnonymousAuthor()
    {
        // Setter un anonymous
        $task = $this->taskRepository->findOneBy(['id' => 33]);
        $task->setUser(null);

        // Recuperer un user
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/33/delete');
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        self::assertContains('Access Denied. (403 Forbidden)', [$crawler->filter('title')->text()]);
    }

    /************************************* Test Delete Success **********************************************/

    public function testDeleteTaskByAdminOnAnonymousAuthor()
    {
        // Setter un anonymous
        $task = $this->taskRepository->findOneBy(['id' => 33]);
        $task->setUser(null);

        // Recuperer un admin
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/tasks/33/delete');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        self::assertEquals('task_delete', $this->client->getRequest()->get('_route'));
        $crawler = $this->client->followRedirect();
        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! La tâche a bien été supprimée.', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);
        $this->assertNull($this->taskRepository->findOneBy(['id' => 33]));
    }

    public function testDeleteTaskByUserAsAuthor()
    {
        // Recupère un user
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);

        // Setter le user as Author
        $task = $this->taskRepository->findOneBy(['id' => 44]);
        $task->setUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/44/delete');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        self::assertEquals('task_delete', $this->client->getRequest()->get('_route'));
        $crawler = $this->client->followRedirect();
        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! La tâche a bien été supprimée.', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);
        $this->assertNull($this->taskRepository->findOneBy(['id' => 44]));
    }
}

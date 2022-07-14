<?php

namespace App\Tests\Controller\TaskController;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Controller\AbstractControllerTest;

class AccessTaskPagesControllerTest extends AbstractControllerTest
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

    /********************************** Access List Task Done && Undone ***************************************/

    public function testListUndoneAsNonUser()
    {
        $crawler = $this->client->request('GET', '/tasks/undone');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testListDoneAsNonUser()
    {
        $crawler = $this->client->request('GET', '/tasks/done');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testListUndoneAsUser()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/undone');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        self::assertContains('Marquer comme faite', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
        self::assertNotContains($crawler->filter('i.fa.fa-close.fa-lg'), [$crawler]);
    }

    public function testListDoneAsUser()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/done');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        self::assertContains('Marquer non terminée', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
        self::assertNotContains($crawler->filter('i.fa.fa-times.fa-lg'), [$crawler]);
    }

    /********************************** Acces Edit Page ******************************************************/

    public function testAccessEditTaskAsNonUser()
    {
        $crawler = $this->client->request('GET', '/tasks/11/edit');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testAccessEditTaskAsUser()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/11/edit');
        $task = $this->taskRepository->findOneBy(['id' => 11]);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Modifier ' . $task->getTitle(), [$crawler->filter('h1')->text()]);
        self::assertContains('Modifier', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }

    /************************************* Access Page Create *****************************************************/

    public function testAccessCreateTaskAsNonUser()
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testAccessCreateTaskAsUser()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/create');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer une nouvelle tache', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }
}

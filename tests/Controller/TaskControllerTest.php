<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\DomCrawler\Crawler;

class TaskControllerTest extends AbstractControllerTest
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

    /********************************** List Task Done && Undone ***************************************/

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

    public function listUndoneAsUser(): Crawler
    {
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/undone');

        return $crawler;
    }
    public function testListUndoneAsUser()
    {
        $crawler = $this->listUndoneAsUser();
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        self::assertContains('Marquer comme faite', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
        self::assertNotContains($crawler->filter('i.fa.fa-close.fa-lg'), [$crawler]);
    }

    public function listDoneAsUser(): Crawler
    {
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/done');

        return $crawler;
    }
    public function testListDoneAsUser()
    {
        $crawler = $this->listDoneAsUser();
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        self::assertContains('Marquer non terminée', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
        self::assertNotContains($crawler->filter('i.fa.fa-times.fa-lg'), [$crawler]);
    }

    /****************************** Acces Page Create *****************************************************/

    public function testAccessCreateTaskAsNonUser()
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function accessCreateTaskAsUser()
    {
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/create');

        return $crawler;
    }

    public function testAccessCreateTaskAsUser()
    {
        $crawler = $this->accessCreateTaskAsUser();
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer une nouvelle tache', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }

    /******************************** Create Task ******************************************************/

    public function testCreateTaskWithValidData()
    {
        $crawler = $this->accessCreateTaskAsUser();
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'New Task for create toggle and delete test',
            'task[content]' => 'New content for new task'
        ]);
        $this->client->submit($form);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
//        dd($this->client->getResponse()->getContent());
        self::assertContains('Superbe ! La tâche a été bien été ajoutée.', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', [$crawler->filter('h1')->text()]);

        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
        self::assertInstanceOf(Task::class, $testTask);
        self::assertEquals('New Task for create toggle and delete test', $testTask->getTitle());
        self::assertEquals('New content for new task', $testTask->getContent());
    }

    public function testCreateTaskWithEmptyTitle()
    {
        $crawler = $this->accessCreateTaskAsUser();
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => '',
            'task[content]' => 'New content for new task'
        ]);
        $form->disableValidation();
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer une nouvelle tache', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->taskRepository->findOneBy(['title' => '']));
    }

    public function testCreateTaskWithEmptyContent()
    {
        $crawler = $this->accessCreateTaskAsUser();
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'New Task for empty content test',
            'task[content]' => ''
        ]);
        $form->disableValidation();
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer une nouvelle tache', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->taskRepository->findOneBy(['title' => 'New Task for empty content test']));
    }

    /********************************** Acces Edit Task ******************************************************/

    public function testAccessEditTaskAsNonUser()
    {
        $crawler = $this->client->request('GET', '/tasks/11/edit');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function accessEditTaskAsUser()
    {
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/11/edit');

        return $crawler;
    }

    public function testAccessEditTaskAsUser()
    {
        $task = $this->taskRepository->findOneBy(['id' => 11]);
        $crawler = $this->accessEditTaskAsUser();
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Modifier ' . $task->getTitle(), [$crawler->filter('h1')->text()]);
        self::assertContains('Modifier', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }

    /******************************************* Edit Task *******************************************/

    public function testEditTaskSuccessfully(): void
    {
        $crawler = $this->accessEditTaskAsUser();
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'task[title]' => 'Entry of new title for edit task test',
            'task[content]' => 'Entry of new content for edit task test'
        ]);
        $this->client->submit($form);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! La tâche a bien été modifiée.', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Bienvenue sur Todo List, l\'application vous permettant de gérer l\'ensemble de vos tâches sans effort !', [$crawler->filter('h1')->text()]);

        $testTask = $this->taskRepository->findOneBy(['id' => 11]);
        self::assertInstanceOf(Task::class, $testTask);
        self::assertEquals('Entry of new title for edit task test', $testTask->getTitle());
        self::assertEquals('Entry of new content for edit task test', $testTask->getContent());
    }

/************************************ Toggle  *******************************************************************/

    public function testToggleTaskAsNonUser()
    {
        $crawler = $this->client->request('GET', '/tasks/11/toggle');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testToggleTaskFromListUnDone()
    {
        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
        self::assertSame(false, $testTask->isDone());

        $crawler = $this->listUndoneAsUser();
        self::assertContains('Marquer comme faite', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
        $form = $crawler->selectButton('Marquer comme faite')->form();
        $this->client->submit($form);

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! La tâche New Task for create toggle and delete test a bien été marquée comme terminée', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);

        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
        self::assertSame(true, $testTask->isDone());
    }

    public function testToggleTaskFromListDone()
    {
        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
        self::assertSame(true, $testTask->isDone());

        $crawler = $this->listDoneAsUser();
        self::assertContains('Marquer non terminée', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
        $form = $crawler->selectButton('Marquer non terminée')->form();
        $this->client->submit($form);

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! La tâche New Task for create toggle and delete test a bien été marquée comme non terminée.', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);

        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
        self::assertSame(false, $testTask->isDone());
    }


    /***************************************** Delete ***********************************************************/
// La tache 11 est crée par le user : user. La tache 12 est crée par le user : admin

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
        $testUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);

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
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);

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
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/33/delete');
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
        self::assertContains('Access Denied. (403 Forbidden)', [$crawler->filter('title')->text()]);
    }

    public function testDeleteTaskByAdminOnAnonymousAuthor()
    {
        // Setter un anonymous
        $task = $this->taskRepository->findOneBy(['id' => 33]);
        $task->setUser(null);
        // Recuperer un admin
        $testUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);

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
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);

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

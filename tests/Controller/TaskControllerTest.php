<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;

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

    /****************************** Acces Page Create *****************************************************/

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

    /******************************** Create Task ******************************************************/

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

    /********************************** Acces Edit Task ******************************************************/

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

    /******************************************* Edit Task *******************************************/

    public function testEditTaskSuccessfully(): void
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/11/edit');
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

    public function testToogleUndoneTask()
    {
        // Setter la task comme undone
        $testTask = $this->taskRepository->findOneBy(['id' => 22]);
        $testTask->setAsUndone();
        $taskTitle = $testTask->getTitle();
        self::assertSame(false, $testTask->isDone());

        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/22/toggle');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        self::assertEquals('task_toggle', $this->client->getRequest()->get('_route'));
        $crawler = $this->client->followRedirect();
        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! La tâche '.$taskTitle.' a bien été marquée comme terminée', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);

        // Check Task comme done
        $testTask = $this->taskRepository->findOneBy(['title' => $taskTitle]);
        self::assertSame(true, $testTask->isDone());
    }

    public function testToogleDoneTask()
    {
        // Setter la task comme done
        $testTask = $this->taskRepository->findOneBy(['id' => 22]);
        $testTask->setAsDone();
        $taskTitle = $testTask->getTitle();
        self::assertSame(true, $testTask->isDone());

        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/22/toggle');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        self::assertEquals('task_toggle', $this->client->getRequest()->get('_route'));
        $crawler = $this->client->followRedirect();
        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! La tâche '.$taskTitle.' a bien été marquée comme non terminée.', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);

        // Check Task comme undone
        $testTask = $this->taskRepository->findOneBy(['title' => $taskTitle]);
        self::assertSame(false, $testTask->isDone());
    }



    /*
        DISQUALIFICATION DE DEUX TESTS :
        Les tests testToggleTaskFromListUnDone() et testToggleTaskFromListDone() sont des tests très interressants.
        Ils permettent de toggle la tache non pas par l'url mais par les boutons 'Marquer comme faite' 'Marquer comme
        non faite' via le navigateur. Ainsi ils simulent le clic, la soumission du formulaire et le changement
        d'etat de la tache.
        Ils ne peuvent cependant pas être retenu car ils ne sont pas independants, ils dependent du test de création
        de la tache réalisé plus haut. En effet via le crawler, on selectionne le premier bouton
        'Marquer comme faite' qui est le bouton de la tache la plus recente cad celle que l'on a crée plus haut.

        On a pas réussi à trouver le moyen de selectionner le bouton d'une tache précise ( genre id = 33 ).
    */
//    public function testToggleTaskFromListUnDone()
//    {
//        $this->getUser();
//
//
//        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
//        self::assertSame(false, $testTask->isDone());
//
//        $this->getUser();
//        $crawler = $this->client->request('GET', '/tasks/undone');
//        self::assertContains('Marquer comme faite', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
//        $form = $crawler->selectButton('Marquer comme faite')->form();
//        $this->client->submit($form);
//
//        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
//
//        $crawler = $this->client->followRedirect();
//
//        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
//        self::assertContains('Superbe ! La tâche New Task for create toggle and delete test a bien été marquée comme terminée', [$crawler->filter('div.alert.alert-success')->text()]);
//        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
//        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);
//
//        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
//        self::assertSame(true, $testTask->isDone());
//    }
//
//    public function testToggleTaskFromListDone()
//    {
//        $this->getUser();
//
//        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
//        self::assertSame(true, $testTask->isDone());
//
//        $crawler = $this->client->request('GET', '/tasks/done');
//        self::assertContains('Marquer non terminée', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
//        $form = $crawler->selectButton('Marquer non terminée')->form();
//        $this->client->submit($form);
//
//        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
//
//        $crawler = $this->client->followRedirect();
//
//        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
//        self::assertContains('Superbe ! La tâche New Task for create toggle and delete test a bien été marquée comme non terminée.', [$crawler->filter('div.alert.alert-success')->text()]);
//        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
//        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);
//
//        $testTask = $this->taskRepository->findOneBy(['title' => 'New Task for create toggle and delete test']);
//        self::assertSame(false, $testTask->isDone());
//    }


    /***************************************** Delete ***********************************************************/

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

<?php

namespace App\Tests\Controller\TaskController;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Controller\AbstractControllerTest;

class UpdateTaskControllerTest extends AbstractControllerTest
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

/*********************************************   Toggle  ****************************************************/

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

        $testTask = $this->taskRepository->findOneBy(['id' => 22]);
        self::assertSame(true, $testTask->isDone());
    }

    public function testToogleDoneTask()
    {
        // Setter la task comme done
        $testTask = $this->taskRepository->findOneBy(['id' => 88]);
        $testTask->setAsDone();
        $taskTitle = $testTask->getTitle();
        self::assertSame(true, $testTask->isDone());

        $this->getUser();

        $crawler = $this->client->request('GET', '/tasks/88/toggle');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        self::assertEquals('task_toggle', $this->client->getRequest()->get('_route'));
        $crawler = $this->client->followRedirect();
        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! La tâche '.$taskTitle.' a bien été marquée comme non terminée.', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);

        // Check Task comme undone
        $testTask = $this->taskRepository->findOneBy(['id' => 88]);
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

}

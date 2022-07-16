<?php

namespace App\Tests\Controller\UserController;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Controller\AbstractControllerTest;

class EditUserControllerTest extends AbstractControllerTest
{
//    /** @var TaskRepository */
    protected TaskRepository $taskRepository;
//    /** @var UserRepository */
    protected UserRepository $userRepository;

    protected function setUp(): void {
        parent::setUp();
        $this->taskRepository = $this->getContainer()->get(TaskRepository::class);
        $this->userRepository = $this->getContainer()->get(UserRepository::class);
    }

    /******************************************* Edit User **********************************************************/

    public function testEditUserSuccessfully(): void {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users/8/edit');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'editTest',
            'user[password][first]' => 'editTest',
            'user[password][second]' => 'editTest',
            'user[email]' => 'editTest@editTest.fr',
            'user[roles]' => 'ROLE_ADMIN'
        ]);
        $this->client->submit($form);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        self::assertEquals('user_list', $this->client->getRequest()->get('_route'));
        self::assertContains('Superbe ! L\'utilisateur a bien été modifié', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Liste des utilisateurs', [$crawler->filter('h1')->text()]);
        self::assertContains('Créer un utilisateur', [$crawler->filter('a.btn.btn-info.pull-right')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);

        $testUser = $this->userRepository->findOneBy(['username' => 'editTest']);
        self::assertInstanceOf(User::class, $testUser);
        self::assertEquals('editTest', $testUser->getUsername());
        self::assertEquals('editTest@editTest.fr', $testUser->getEmail());
        self::assertContains('ROLE_ADMIN', $testUser->getRoles());
        self::assertSame(8, $testUser->getId());
    }
}

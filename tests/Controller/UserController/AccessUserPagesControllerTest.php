<?php

namespace App\Tests\Controller\UserController;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Controller\AbstractControllerTest;

class AccessUserPagesControllerTest extends AbstractControllerTest
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

    /******************************** Access Page List **************************************************************/

    public function testListAsNonUser()
    {
        $crawler = $this->client->request('GET', '/users');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testListAsAdmin()
    {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Liste des utilisateurs', [$crawler->filter('h1')->text()]);
        self::assertContains('Créer un utilisateur', [$crawler->filter('a.btn.btn-info.pull-right')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }

    public function testListAsUser()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/users');
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
        self::assertContains('Page Introuvable (404 Not Found)', [$crawler->filter('title')->text()]);
    }

    /************************************* Access Page Create *****************************************************/

    public function testAccessCreatePageAsNonUser()
    {
        $crawler = $this->client->request('GET', '/users/create');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testAccessCreatePageAsAdmin()
    {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }

    public function testAccessCreatePageAsUser()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/users/create');
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
        self::assertContains('Page Introuvable (404 Not Found)', [$crawler->filter('title')->text()]);
    }

    /************************************* Test Acces Edit *****************************************************/

    public function testAccessEditPageAsNonUser()
    {
        $crawler = $this->client->request('GET', '/users/2/edit');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testAccessEditPageAsAdmin()
    {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users/2/edit');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Modifier user', [$crawler->filter('h1')->text()]);
        self::assertContains('Modifier', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }

    public function testAccessEditPageAsUser()
    {
        $this->getUser();

        $crawler = $this->client->request('GET', '/users/2/edit');
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
        self::assertContains('Page Introuvable (404 Not Found)', [$crawler->filter('title')->text()]);
    }
}

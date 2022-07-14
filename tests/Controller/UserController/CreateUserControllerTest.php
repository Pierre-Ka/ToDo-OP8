<?php

namespace App\Tests\Controller\UserController;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Controller\AbstractControllerTest;

class CreateUserControllerTest extends AbstractControllerTest
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

    public function testCreateUserWithValidData()
    {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'firstTest',
            'user[password][first]' => 'firstTest',
            'user[password][second]' => 'firstTest',
            'user[email]' => 'firstTest@firstTest.fr',
            'user[roles]' => 'ROLE_USER'
        ]);

        $this->client->submit($form);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        self::assertEquals('user_list', $this->client->getRequest()->get('_route'));
//        dd($this->client->getResponse()->getContent());
        self::assertContains('Superbe ! L\'utilisateur a bien été ajouté.', [$crawler->filter('div.alert.alert-success')->text()]);
        self::assertContains('Liste des utilisateurs', [$crawler->filter('h1')->text()]);
        self::assertContains('Créer un utilisateur', [$crawler->filter('a.btn.btn-info.pull-right')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);

        $testUser = $this->userRepository->findOneBy(['username' => 'firstTest']);
        self::assertInstanceOf(User::class, $testUser);
        self::assertEquals('firstTest', $testUser->getUsername());
        self::assertEquals('firstTest@firstTest.fr', $testUser->getEmail());
        self::assertEquals('ROLE_USER', $testUser->getRoles()[0]);
    }

    /************************************* Test Create Fail **********************************************/

    public function testCreateUserWithPassportsUnmatch()
    {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'secondTest',
            'user[password][first]' => 'secondTest',
            'user[password][second]' => 'erreur',
            'user[email]' => 'secondTest@secondTest',
            'user[roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());

//        <label for="user_password_first" class="required">
//          Mot de passe
//            <span class="invalid-feedback d-block">
//                  <span class="d-block">\n
//                          <span class="form-error-icon badge badge-danger text-uppercase">Error</span>
//                          <span class="form-error-message">Les deux mots de passe doivent correspondre.</span>\n
//                   </span>
//            </span>
//        </label>
//        De manière extraordinaire, la validation Symfony renvoie l'erreur ci dessus. Cepedant le crawler recupéré lors de la
//        generation de la page ne contient pas de <span> dans le <label></label>. Ainsi en jouant le test ci-dessous,
//        on a 0 qui s'affiche
//        dd($crawler->filter('label.required')->eq(1)->children('span')->count());
//        Finalement on a ceci : le crawler symfony ne supporte pas le JS.

        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
    }

    public function testCreateUserWithInvalidEmail()
    {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'thirdTest',
            'user[password][first]' => 'thirdTest',
            'user[password][second]' => 'thirdTest',
            'user[email]' => 'thirdTest.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
    }

    public function testCreateUserWithInvalidPassword()
    {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'fourthTest',
            'user[password][first]' => 'fo',
            'user[password][second]' => 'fo',
            'user[email]' => 'fourthTest.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
    }

    public function testCreateUserWithEmailAlreadyUse()
    {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'fiveTest',
            'user[password][first]' => 'fiveTest',
            'user[password][second]' => 'fiveTest',
            'user[email]' => 'user@user.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
    }

    public function testCreateUserWithUsernameAlreadyUse()
    {
        $this->getAdmin();

        $crawler = $this->client->request('GET', '/users/create');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'admin',
            'user[password][first]' => 'sixTest',
            'user[password][second]' => 'sixTest',
            'user[email]' => 'sixTest@sixTest.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
    }
}

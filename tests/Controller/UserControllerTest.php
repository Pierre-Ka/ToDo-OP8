<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;

class UserControllerTest extends AbstractControllerTest
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
    /******************************** Page List **********************************************************************/
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
        $testUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users');

        //        dd($this->client->getResponse()->getContent());
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Liste des utilisateurs', [$crawler->filter('h1')->text()]);
        self::assertContains('Créer un utilisateur', [$crawler->filter('a.btn.btn-info.pull-right')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }

    public function testListAsUser()
    {
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users');
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
        self::assertContains('Page Introuvable (404 Not Found)', [$crawler->filter('title')->text()]);
    }

    /************************************* Acces Page Create *****************************************************/

    public function testAccessCreatePageAsNonUser()
    {
        $crawler = $this->client->request('GET', '/users/create');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function accessCreateUserPageAsAdmin()
    {
        $testUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users/create');

        return $crawler;
    }

    public function testAccessCreatePageAsAdmin()
    {
        $crawler = $this->accessCreateUserPageAsAdmin();

        //        dd($this->client->getResponse()->getContent());
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }

    public function testAccessCreatePageAsUser()
    {
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users/create');
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
        self::assertContains('Page Introuvable (404 Not Found)', [$crawler->filter('title')->text()]);
    }

    /************************************* Test Create *****************************************************/
    public function testCreateUserWithValidData()
    {
        $crawler = $this->accessCreateUserPageAsAdmin();
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

    public function testCreateUserWithPassportsUnmatch()
    {
        $crawler = $this->accessCreateUserPageAsAdmin();
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'secondTest',
            'user[password][first]' => 'secondTest',
            'user[password][second]' => 'erreur',
            'user[email]' => 'secondTest@secondTest',
            'user[roles]' => 'ROLE_USER'
        ]);
        $form->disableValidation();
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

        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
    }

    public function testCreateUserWithInvalidEmail()
    {
        $crawler = $this->accessCreateUserPageAsAdmin();
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'thirdTest',
            'user[password][first]' => 'thirdTest',
            'user[password][second]' => 'thirdTest',
            'user[email]' => 'thirdTest.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $form->disableValidation();
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
    }

    public function testCreateUserWithInvalidPassword()
    {
        $crawler = $this->accessCreateUserPageAsAdmin();
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'fourthTest',
            'user[password][first]' => 'fo',
            'user[password][second]' => 'fo',
            'user[email]' => 'fourthTest.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $form->disableValidation();
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
    }


    public function testCreateUserWithEmailAlreadyUse()
    {
        $crawler = $this->accessCreateUserPageAsAdmin();
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'fiveTest',
            'user[password][first]' => 'fiveTest',
            'user[password][second]' => 'fiveTest',
            'user[email]' => 'user@user.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $form->disableValidation();
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
    }

    public function testCreateUserWithUsernameAlreadyUse()
    {
        $crawler = $this->accessCreateUserPageAsAdmin();
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            'user[username]' => 'admin',
            'user[password][first]' => 'sixTest',
            'user[password][second]' => 'sixTest',
            'user[email]' => 'sixTest@sixTest.fr',
            'user[roles]' => 'ROLE_USER'
        ]);
        $form->disableValidation();
        $this->client->submit($form);
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Créer un utilisateur', [$crawler->filter('h1')->text()]);
        self::assertContains('Ajouter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        $this->assertNull($this->userRepository->findOneBy(['username' => 'secondTest']));
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

    public function accessEditUserPageAsAdmin()
    {
        $testUser = $this->userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users/2/edit');

        return $crawler;
    }

    public function testAccessEditPageAsAdmin()
    {
        $crawler = $this->accessEditUserPageAsAdmin();

        //        dd($this->client->getResponse()->getContent());
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Modifier user', [$crawler->filter('h1')->text()]);
        self::assertContains('Modifier', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
    }

    public function testAccessEditPageAsUser()
    {
        $testUser = $this->userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users/2/edit');
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
        self::assertContains('Page Introuvable (404 Not Found)', [$crawler->filter('title')->text()]);
    }

    /******************************************* Edit User **********************************************************/

    public function testEditUserSuccessfully(): void
    {
        $crawler = $this->accessEditUserPageAsAdmin();
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
        self::assertEquals('ROLE_ADMIN', $testUser->getRoles()[0]);
        self::assertSame(2, $testUser->getId());
    }
}
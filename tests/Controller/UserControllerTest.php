<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends AbstractControllerTest
{
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
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['username' => 'admin']);
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
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/users');
        self::assertEquals(404, $this->client->getResponse()->getStatusCode());
        self::assertContains('Page Introuvable (404 Not Found)', [$crawler->filter('title')->text()]);
    }


//    public function testCreate()
//    {
//        // acceder a la page de création en tant qu'admin
//        // acceder en tant que user
//        // acceder en tant que non connecter
//
//        // creer un utilisateur avec des données valide
//
//        // creer un utilisateur avec un email non valide
//
//        // creer un utilisateur avec un mot de passe trop court
//
//        // creer un utilisateur avec un username deja utilisé
//
//        // creer un utilisateur avec un email deja utilisé
//
//    }
//
//    public function testEdit()
//    {
//
//    }
//
}
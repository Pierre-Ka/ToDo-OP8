<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends AbstractControllerTest
{
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
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/undone');

//        dd($crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text());
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        self::assertContains('Marquer comme faite', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
        self::assertNotContains($crawler->filter('i.fa.fa-close.fa-lg'), [$crawler]);
    }

    public function testListDoneAsUser()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/done');

//        dd($crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text());
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Retour à la page d\'accueil', [$crawler->filter('a.btn.btn-secondary')->text()]);
        self::assertContains('Marquer non terminée', [$crawler->filter('button.btn.btn-success.btn-sm.pull-right')->text()]);
        self::assertNotContains($crawler->filter('i.fa.fa-times.fa-lg'), [$crawler]);
    }

    // Task create
    // Task edit
    // Task toggle
    // Task Delete
}
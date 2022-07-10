<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\Controller\AbstractControllerTest;


class SecurityControllerTest extends AbstractControllerTest
{
    public function setUp(): void
    {
        $this->client = self::createClient();
    }

    public function testLoginAsAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        // De manière étrange, assertContains n'accepte pas haystack en string mais seulement en iterable
        // Du coup, le tableau ça marche
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        $this->loginAsAdmin();

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

//        dd($this->client->getResponse()->getContent());
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);
        self::assertContains('Consulter la liste des utilisateurs', [$crawler->filter('#test-for-admin')->text()]);
    }

    public function testLoginAsUser(): void
    {
        $crawler = $this->client->request('GET', '/login');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        $this->loginAsUser();

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

//        dd($this->client->getResponse()->getContent());
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);
        self::assertNotContains($crawler->filter('#test-for-admin'), [$crawler]);
    }

    public function testLoginFailure(): void
    {
        $crawler = $this->client->request('GET', '/login');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        $this->loginAsNonUser();

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();

        //        dd($this->client->getResponse()->getContent());
        self::assertContains('Invalid credentials.', [$crawler->filter('div.alert.alert-danger')->text()]);
        $this->assertStringContainsString('Se Connecter', '' . $this->client->getResponse()->getContent());
        self::assertNotContains($crawler->filter('#test-for-admin'), [$crawler]);
    }

    public function testLogout()
    {
        $crawler = $this->indexAsUser();
        $link = $crawler->selectLink('Se deconnecter')->link();
        $this->client->click($link);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();    // redirect vers /
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();    // redirect vers /login

//        dd($this->client->getResponse()->getContent());
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }
}

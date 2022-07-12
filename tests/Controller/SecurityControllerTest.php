<?php

namespace App\Tests\Controller;

//        dd($this->client->getResponse()->getContent());
class SecurityControllerTest extends AbstractControllerTest
{
    public function testLoginAsAdmin(): void
    {
        $this->loginAsAdmin();

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);
        self::assertContains('Consulter la liste des utilisateurs', [$crawler->filter('#test-for-admin')->text()]);
    }

    public function testLoginAsUser(): void
    {
        $this->loginAsUser();

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);
        self::assertNotContains($crawler->filter('#test-for-admin'), [$crawler]);
    }

    public function testLoginFailure(): void
    {
        $this->loginAsNonUser();

        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('login', $this->client->getRequest()->get('_route'));
        self::assertContains('Invalid credentials.', [$crawler->filter('div.alert.alert-danger')->text()]);
        self::assertStringContainsString('Se Connecter', '' . $this->client->getResponse()->getContent());
        self::assertNotContains($crawler->filter('#test-for-admin'), [$crawler]);
    }

    public function testLogout()
    {
        $this->getUser();
        $crawler = $this->client->request('GET', '/');

        $link = $crawler->selectLink('Se deconnecter')->link();
        $this->client->click($link);
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('homepage', $this->client->getRequest()->get('_route'));
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertEquals('login', $this->client->getRequest()->get('_route'));
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }
}

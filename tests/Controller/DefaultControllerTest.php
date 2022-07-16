<?php

namespace App\Tests\Controller;

class DefaultControllerTest extends AbstractControllerTest
{
    public function testIndexAsNonUser(): void {
        $crawler = $this->client->request('GET', '/');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);
    }

    public function testIndexAsUser(): void {
        $this->getUser();
        $crawler = $this->client->request('GET', '/');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);
    }
}

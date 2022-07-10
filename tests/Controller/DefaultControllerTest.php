<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends AbstractControllerTest
{
    public function testIndexAsNonUser(): void
    {
        $crawler = $this->client->request('GET', '/');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        self::assertContains('Entrer votre email :', [$crawler->filter('label')->text()]);

    }

    public function testIndexAsUser(): void
    {
        $this->IndexAsUser();
    }
}
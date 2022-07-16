<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void {
        $this->client = static::createClient();
    }

    public function getUser(): void {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
    }

    public function getAdmin(): void {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);
    }

    public function loginAsAdmin(): void {
        $crawler = $this->client->request('GET', '/login');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            '_username' => 'admin@admin.fr',
            '_password' => 'secret'
        ]);
        $this->client->submit($form);
    }

    public function loginAsUser(): void {
        $crawler = $this->client->request('GET', '/login');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            '_username' => 'user@user.fr',
            '_password' => 'secret'
        ]);
        $this->client->submit($form);
    }

    public function loginAsNonUser(): void {
        $crawler = $this->client->request('GET', '/login');
        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Se connecter', [$crawler->filter('button.btn.btn-success')->text()]);
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            '_username' => 'inconnu@inconnu.fr',
            '_password' => 'absent'
        ]);
        $this->client->submit($form);
    }
}

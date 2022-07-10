<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractControllerTest extends WebTestCase
{
    // GetAdmin

    // GetUser

    // GetUnknow

    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function loginAsAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            '_username' => 'admin@admin.fr',
            '_password' => 'secret'
        ]);

        $this->client->submit($form);
    }

    public function loginAsUser(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            '_username' => 'user@user.fr',
            '_password' => 'secret'
        ]);

        $this->client->submit($form);
    }

    public function loginAsNonUser(): void
    {
        $crawler = $this->client->request('GET', '/login');

        $buttonCrawlerMode = $crawler->filter('form');
        $form = $buttonCrawlerMode->form([
            '_username' => 'inconnu@inconnu.fr',
            '_password' => 'absent'
        ]);

        $this->client->submit($form);
    }

    public function IndexAsUser(): Crawler
    {
//        $userRepository = self::$container->get(UserRepository::class);
//        De maniere etrange, ce code donne :
//        Error: Access to undeclared static property App\Tests\Controller\SecurityControllerTest::$container
//        $this->loginAsUser();

        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);

        return $crawler ;
    }
}

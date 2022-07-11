<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class AbstractControllerTest extends WebTestCase
{
    protected $client;
//    /** @var TaskRepository */
//    protected $taskRepository;
//    /** @var UserRepository */
//    protected $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
//        $this->taskRepository = $this->getContainer()->get(TaskRepository::class);
//        $this->userRepository = $this->getContainer()->get(UserRepository::class);
    }

    public function getUser()
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['username' => 'user']);
        $this->client->loginUser($testUser);
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
        $userRepository = $this->client->getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username' => 'admin']);
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/');

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertContains('Que souhaitez-vous faire maintenant ?', [$crawler->filter('h2')->text()]);
        self::assertContains('Consulter la liste des tâches terminées', [$crawler->filter('a.btn.btn-secondary.btn-md')->text()]);

        return $crawler ;
    }
}

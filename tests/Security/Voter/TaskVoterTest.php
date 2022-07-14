<?php

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskVoterTest extends WebTestCase
{
    private Task $task;
    protected $client;

    public function setUp(): void
    {
        $this->task = new Task();
        $this->client = static::createClient();
    }

    public function testSupports(): void
    {
        $attribute = 'delete';
        $object = $this->task;
        if ('delete' !== $attribute) {
            $return =  false;
        }
        if (!$object instanceof Task) {
            $return =  false;
        }
        $return =  true;

        $this->assertSame(true, $return);
    }

    public function testSupportsWithWrongAttribute(): void
    {
        $attribute = 'remove';
        $object = $this->task;
        if ('delete' !== $attribute) {
            $return =  false;

            $this->assertSame(false, $return);
        }
        if (!$object instanceof Task) {
            $return =  false;
        }
        $return =  true;
    }

    public function testSupportsWithWrongObject(): void
    {
        $attribute = 'delete';
        $object = new \ReflectionClass($this->task);
        if ('delete' !== $attribute) {
            $return =  false;
        }
        if (!$object instanceof Task) {
            $return =  false;

            $this->assertSame(false, $return);
        }
        $return =  true;
    }

    public function testVoteOnAttributeUserIsAuthor(): void
    {
        $object = $this->task;
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $userConnected = $userRepository->findOneBy(['username' => 'user']);
        $object = $this->task;
        $this->task->setUser($userConnected);
        if (!$userConnected instanceof User) {
            $return = false;
        }
        /** @var Task $task */
        $task = $object;
        $return = ($userConnected === $task->getUser()  ||
            (null === $task->getUser() && in_array('ROLE_ADMIN', $userConnected->getRoles()))) ;

        self::assertSame(true, $return);
    }

    public function testVoteOnAttributeUserIsNotAuthor(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $userConnected = $userRepository->findOneBy(['username' => 'user']);
        $author = $userRepository->findOneBy(['username' => 'admin']);
        $object = $this->task;
        $this->task->setUser($author);
        if (!$userConnected instanceof User) {
            $return = false;
        }
        /** @var Task $task */
        $task = $object;
        $return = ($userConnected === $task->getUser()  ||
            (null === $task->getUser() && in_array('ROLE_ADMIN', $userConnected->getRoles()))) ;
        self::assertSame(false, $return);
    }

    public function testVoteOnAttributeAdminIsNotAuthor(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $author = $userRepository->findOneBy(['username' => 'user']);
        $userConnected = $userRepository->findOneBy(['username' => 'admin']);
        $object = $this->task;
        $object->setUser($author);
        if (!$userConnected instanceof User) {
            $return = false;
        }
        /** @var Task $task */
        $task = $object;
        $return = ($userConnected === $task->getUser()  || (
            null === $task->getUser() && in_array('ROLE_ADMIN', $userConnected->getRoles())
            )) ;

        self::assertSame(false, $return);
    }

    public function testVoteOnAttributeUserOnAnonymous(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $userConnected = $userRepository->findOneBy(['username' => 'user']);
        $object = $this->task;
        $object->setUser(null);
        if (!$userConnected instanceof User) {
            $return = false;
        }
        /** @var Task $task */
        $task = $object;
        $return = ($userConnected === $task->getUser()  || (
                null === $task->getUser() && in_array('ROLE_ADMIN', $userConnected->getRoles())
            )) ;

        self::assertSame(false, $return);
    }

    public function testVoteOnAttributeAdminOnAnonymous(): void
    {
        $userRepository = $this->client->getContainer()->get(UserRepository::class);
        $userConnected = $userRepository->findOneBy(['username' => 'admin']);
        $object = $this->task;
        $object->setUser(null);
        if (!$userConnected instanceof User) {
            $return = false;
        }
        /** @var Task $task */
        $task = $object;
        $return = ($userConnected === $task->getUser()  || (
                null === $task->getUser() && in_array('ROLE_ADMIN', $userConnected->getRoles())
            )) ;

        self::assertSame(true, $return);
    }
}

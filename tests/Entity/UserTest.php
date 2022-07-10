<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function PHPUnit\Framework\assertSame;

//When you’re testing inside of your PHPUnit test methods, you will use many assert-based methods.
//Two that are seemingly very similar are assertSame() and assertEqual()
//The difference between these is the same as the difference of === and == in PHP.
//One is equal and one is identical.
//assertSame is the closest to identical comparison that you can use.
//So, when you have a choice, use assertSame() instead of equals. This will help catch type mismatches as well.


class UserTest extends KernelTestCase
{
    private User $user;
    private Task $task;
    public function setUp(): void
    {
        $this->user = new User();
        $this->task = new Task();
    }

    public function testUsername(): void
    {
        $this->user->setUsername('Mon pseudo');
        assertSame('Mon pseudo', $this->user->getUsername());
    }
    public function testPassword(): void
    {
        $this->user->setPassword('secret');
        assertSame('secret', $this->user->getPassword());
    }
    public function testEmail(): void
    {
        $this->user->setEmail('email@email.fr');
        assertSame('email@email.fr', $this->user->getEmail());
    }
    public function testRoles(): void
    {
        $this->user->setRoles(['ROLE_TESTEUR']);
        $this->assertIsArray($this->user->getRoles());
        $this->assertEquals(['ROLE_TESTEUR'], $this->user->getRoles());
    }

    public function testTasks(): void
    {
        $this->task->setUser($this->user);
        assertSame($this->task->getUser(), $this->user);

        $this->user->addTask($this->task);
        self::assertCount(1, $this->user->getTasks());
        $this->assertInstanceOf(ArrayCollection::class, $this->user->getTasks());

        $this->user->removeTask($this->task);
        self::assertCount(0, $this->user->getTasks());
    }

    public function testSalt(): void
    {
        self::assertNull($this->user->getSalt());
    }

    public function testUserIdentifier(): void
    {
        $this->user->setEmail('user@user.fr');
        assertSame('user@user.fr', $this->user->getUserIdentifier());
    }
}
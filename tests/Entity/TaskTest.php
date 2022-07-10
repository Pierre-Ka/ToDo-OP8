<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function PHPUnit\Framework\assertSame;

class TaskTest extends KernelTestCase
{
    private Task $task;
    private \DateTime $date;

    public function setUp(): void
    {
        $this->task = new Task();
        $this->date = new \DateTime();
    }

    public function testCreatedAt(): void
    {
        $this->task->setCreatedAt($this->date);
        assertSame($this->date, $this->task->getCreatedAt());
    }

    public function testTitle(): void
    {
        $this->task->setTitle('Ola');
        assertSame('Ola', $this->task->getTitle());
    }

    public function testContent(): void
    {
        $this->task->setTitle('Contenu');
        assertSame('Contenu', $this->task->getTitle());
    }

    public function testisDoneFalse(): void
    {
        $this->assertFalse($this->task->isDone());
    }

    public function testisDoneTrue(): void
    {
        $this->task->toggle(true);
        self::assertEquals(true, $this->task->isDone());
    }

    public function testOwner(): void
    {
        $this->task->setUser(new User());
        self::assertInstanceOf(User::class, $this->task->getUser());
    }

    public function tearDown() : void
    {
//  Avec tearDown tout mes tests passent en Error
//        $this->date = null;
//        $this->task = null;
    }
}
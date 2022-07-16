<?php

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use App\Security\Voter\TaskVoter;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskVoterTest extends WebTestCase
{
    private Task $task;
    protected KernelBrowser $client;
    protected TaskVoter $taskVoter;

    public function setUp(): void {
        $this->task = new Task();
        $this->client = static::createClient();
        $this->taskVoter = new TaskVoter();
    }

    public function testSupports(): void  {
        self::assertSame(true, $this->taskVoter->supports('delete', $this->task));
        $object = new \ReflectionClass($this->task);
        self::assertSame(false, $this->taskVoter->supports('delete', $object));
        self::assertSame(false, $this->taskVoter->supports('remove', $this->task));
    }
}

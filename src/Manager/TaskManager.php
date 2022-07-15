<?php

namespace App\Manager;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class TaskManager implements TaskManagerInterface
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function new(Task $task)
    {
        $task->setUser($this->security->getUser());
        $this->em->persist($task);
        $this->em->flush();
    }
    public function update()
    {
        $this->em->flush();
    }
    public function toggle(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();
    }
    public function delete(Task $task)
    {
        $this->em->remove($task);
        $this->em->flush();
    }
}

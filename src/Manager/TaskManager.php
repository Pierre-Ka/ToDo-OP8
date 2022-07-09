<?php

namespace App\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class TaskManager
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function new($task)
    {
        $task->setUser($this->security->getUser());
        $this->em->persist($task);
        $this->em->flush();
    }
    public function update()
    {
        $this->em->flush();
    }
    public function toggle($task)
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();
    }
    public function delete($task)
    {
        $this->em->remove($task);
        $this->em->flush();
    }
}

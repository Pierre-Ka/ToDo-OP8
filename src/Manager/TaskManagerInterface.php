<?php
namespace App\Manager;

use App\Entity\Task;

interface TaskManagerInterface
{
    public function new(Task $task): void;

    public function update(): void;

    public function toggle(Task $task): void;

    public function delete(Task $task): void;
}

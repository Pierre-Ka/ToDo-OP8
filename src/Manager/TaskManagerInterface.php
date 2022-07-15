<?php
namespace App\Manager;

use App\Entity\Task;

interface TaskManagerInterface
{
    public function new(Task $task);

    public function update();

    public function toggle(Task $task);

    public function delete(Task $task);

}
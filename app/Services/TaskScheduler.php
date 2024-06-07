<?php

namespace App\Services;

use App\Task\Task;

class TaskScheduler
{
  private $tasks = [];

  public function addTask(Task $task)
  {
    $this->tasks[] = $task;
  }

  public function run()
  {
    $date = new \DateTime();

    foreach ($this->tasks as $task) {
      // Comprobar si la tarea está vencida y si no se está ejecutando actualmente.
      if ($task->isDue($date) && !$task->isRunning()) {
        $task->run();
      }
    }
  }

  public function releaseAllLocks()
  {
    foreach ($this->tasks as $task) {
      $task->forceUnlock();
    }
  }
}

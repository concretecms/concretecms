<?php

namespace Concrete\Core\Automation\Process;

use Concrete\Core\Automation\Task\Input\InputInterface;
use Concrete\Core\Entity\Automation\Process;
use Concrete\Core\Entity\Automation\Task;

/**
 * Class ProcessFactory
 *
 * Responsible for creating Concrete\Core\Entity\Automation\Process objects from automation tasks and input.
 */
class ProcessFactory
{

    public function createProcess(Task $task, InputInterface $input = null)
    {
        $process = new Process();
        $process->setTask($task);
        $process->setStarted(time());
        $process->setInput($input);
        return $process;
    }


}

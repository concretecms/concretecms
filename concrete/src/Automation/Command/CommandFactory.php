<?php

namespace Concrete\Core\Automation\Command;

use Concrete\Core\Entity\Automation\Process;

class CommandFactory
{

    public function createCommand(Process $process)
    {
        $controller = $process->getTask()->getController();
        $command = $controller->getCommand($process->getInput());
        return $command;
    }

}

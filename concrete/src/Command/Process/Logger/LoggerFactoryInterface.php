<?php

namespace Concrete\Core\Command\Process\Logger;

use Concrete\Core\Command\Task\Runner\ProcessTaskRunnerInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Entity\Command\Process;

interface LoggerFactoryInterface
{

    public function createFromRunner(ProcessTaskRunnerInterface $runner): ?LoggerInterface;

    public function createFromProcess(Process $process): ?LoggerInterface;

}

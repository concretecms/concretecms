<?php

namespace Concrete\Core\Command\Process\Logger;

use Concrete\Core\Command\Task\Runner\ProcessTaskRunnerInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Entity\Command\Process;

interface LoggerFactoryInterface
{

    public function runnerSupportsLogging(TaskRunnerInterface $runner): bool;

    public function createLogger(ProcessTaskRunnerInterface $runner): LoggerInterface;

}

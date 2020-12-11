<?php

namespace Concrete\Core\Command\Process\Logger;

use Concrete\Core\Command\Task\Runner\ProcessTaskRunnerInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Command\Process;

class StandardLoggerFactory implements LoggerFactoryInterface
{

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function runnerSupportsLogging(TaskRunnerInterface $runner): bool
    {
        if ($runner instanceof ProcessTaskRunnerInterface) {
            if ($this->config->get('concrete.processes.logging.method') == 'file') {
                return true;
            }
        }
        return false;
    }

    public function createLogger(ProcessTaskRunnerInterface $runner): LoggerInterface
    {
        return new FileLogger(
            $this->config->get('concrete.processes.logging.file.directory'),
            $runner->getProcess()
        );
    }


}

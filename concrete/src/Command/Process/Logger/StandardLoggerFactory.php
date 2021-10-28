<?php

namespace Concrete\Core\Command\Process\Logger;

use Concrete\Core\Command\Task\Runner\ProcessTaskRunnerInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Command\Process;
use Illuminate\Filesystem\Filesystem;

class StandardLoggerFactory implements LoggerFactoryInterface
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Repository $config, Filesystem $filesystem)
    {
        $this->config = $config;
        $this->filesystem = $filesystem;
    }

    protected function isLoggingEnabled(): bool
    {
        if ($this->config->get('concrete.processes.logging.method') == 'file') {
            return true;
        }
        return false;
    }

    protected function createLogger(Process $process): LoggerInterface
    {
        return new FileLogger(
            $this->config->get('concrete.processes.logging.file.directory'),
            $process
        );
    }

    public function createFromRunner(TaskRunnerInterface $runner): ?LoggerInterface
    {
        if ($runner instanceof ProcessTaskRunnerInterface && $this->isLoggingEnabled()) {
            return $this->createLogger($runner->getProcess());
        }
        return null;
    }

    public function createFromProcess(Process $process): ?LoggerInterface
    {
        if ($this->isLoggingEnabled()) {
            return $this->createLogger($process);
        }
        return null;
    }

}

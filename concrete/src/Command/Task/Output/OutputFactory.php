<?php

namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Task\Runner\LoggableToFileRunnerInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Config\Repository\Repository;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;

class OutputFactory
{

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function createDashboardOutput(TaskRunnerInterface $runner)
    {
        $fileLogger = $this->getFileLogger($runner);
        if ($fileLogger) {
            return $fileLogger;
        }
        return null;
    }

    protected function getFileLogger(TaskRunnerInterface $runner): ?RunnerFileLogger
    {
        if ($this->config->get('concrete.processes.logging.method') == 'file') {
            if ($runner instanceof LoggableToFileRunnerInterface) {
                return new RunnerFileLogger(
                    $this->config->get('concrete.processes.logging.file.directory'),
                    $runner
                );
            }
        }
        return null;
    }

    public function createConsoleOutput(ConsoleOutputInterface $output, TaskRunnerInterface $runner)
    {
        $fileLogger = $this->getFileLogger($runner);
        if ($fileLogger) {
            $consoleOutput = new AggregateOutput([new ConsoleOutput($output), $fileLogger]);
        } else {
            $consoleOutput = new ConsoleOutput($output);
        }

        return $consoleOutput;
    }

}

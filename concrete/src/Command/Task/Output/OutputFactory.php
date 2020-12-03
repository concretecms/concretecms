<?php

namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Task\Runner\LoggableToFileRunnerInterface;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunnerInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Notification\Mercure\MercureService;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;

class OutputFactory
{

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var MercureService
     */
    protected $mercureService;

    public function __construct(Repository $config, MercureService $mercureService)
    {
        $this->config = $config;
        $this->mercureService = $mercureService;
    }

    public function createDashboardOutput(TaskRunnerInterface $runner): ?OutputInterface
    {
        $fileLogger = $this->getFileLoggerOutput($runner);
        $pushOutput = $this->getPushOutput($runner);
        if ($fileLogger) {
            $outputs[] = $fileLogger;
        }
        if ($pushOutput) {
            $outputs[] = $pushOutput;
        }
        if ($outputs > 0) {
            return new AggregateOutput($outputs);
        }
    }

    protected function getFileLoggerOutput(TaskRunnerInterface $runner): ?RunnerFileLoggerOutput
    {
        if ($this->config->get('concrete.processes.logging.method') == 'file') {
            if ($runner instanceof LoggableToFileRunnerInterface) {
                return new RunnerFileLoggerOutput(
                    $this->config->get('concrete.processes.logging.file.directory'),
                    $runner
                );
            }
        }
        return null;
    }

    protected function getPushOutput(TaskRunnerInterface $runner): ?PushOutput
    {
        if ($runner instanceof ProcessTaskRunnerInterface && $this->mercureService->isEnabled()) {
            return new PushOutput($this->mercureService, $runner->getProcess()->getID());
        }
        return null;
    }

    public function createConsoleOutput(ConsoleOutputInterface $output, TaskRunnerInterface $runner)
    {
        $fileLogger = $this->getFileLoggerOutput($runner);
        $pushOutput = $this->getPushOutput($runner);
        $outputs = [new ConsoleOutput($output)];
        if ($fileLogger) {
            $outputs[] = $fileLogger;
        }
        if ($pushOutput) {
            $outputs[] = $pushOutput;
        }
        if (count($outputs) === 1) {
            return $outputs[0];
        } else {
            return new AggregateOutput($outputs);
        }
    }

}

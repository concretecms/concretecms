<?php

namespace Concrete\Core\Command\Task\Output;

use Concrete\Core\Command\Process\Logger\LoggerFactoryInterface;
use Concrete\Core\Command\Process\Logger\LoggerInterface;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunnerInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Notification\Mercure\MercureService;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;

class OutputFactory
{

    /**
     * @var MercureService
     */
    protected $mercureService;

    /**
     * @var LoggerFactoryInterface
     */
    protected $loggerFactory;

    public function __construct(LoggerFactoryInterface $loggerFactory, MercureService $mercureService)
    {
        $this->loggerFactory = $loggerFactory;
        $this->mercureService = $mercureService;
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
        $processLogger = $this->loggerFactory->createFromRunner($runner);
        $pushOutput = $this->getPushOutput($runner);
        $outputs = [new ConsoleOutput($output)];
        if ($processLogger) {
            $outputs[] = $processLogger;
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

    public function createDashboardOutput(TaskRunnerInterface $runner): OutputInterface
    {
        $processLogger = $this->loggerFactory->createFromRunner($runner);
        $pushOutput = $this->getPushOutput($runner);
        if ($processLogger) {
            $outputs[] = $processLogger;
        }
        if ($pushOutput) {
            $outputs[] = $pushOutput;
        }
        if ($outputs > 0) {
            return new AggregateOutput($outputs);
        } else {
            return new NullOutput();
        }
    }



}

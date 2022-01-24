<?php
namespace Concrete\Core\Command\Task\Runner\Context;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Process\Logger\LoggerFactoryInterface;
use Concrete\Core\Command\Task\Output\AggregateOutput;
use Concrete\Core\Command\Task\Output\ConsoleOutput;
use Concrete\Core\Command\Task\Output\NullOutput;
use Concrete\Core\Command\Task\Output\PushOutput;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunnerInterface;
use Concrete\Core\Command\Task\Runner\TaskRunnerInterface;
use Concrete\Core\Notification\Events\MercureService;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\OutputInterface as ConsoleOutputInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ContextFactory
{

    /**
     * @var MercureService
     */
    protected $mercureService;

    /**
     * @var LoggerFactoryInterface
     */
    protected $loggerFactory;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(LoggerFactoryInterface $loggerFactory, MercureService $mercureService, Application $app)
    {
        $this->loggerFactory = $loggerFactory;
        $this->mercureService = $mercureService;
        $this->app = $app;
    }

    protected function getPushOutput(TaskRunnerInterface $runner): ?PushOutput
    {
        if ($runner instanceof ProcessTaskRunnerInterface && $this->mercureService->isEnabled()) {
            return new PushOutput($this->mercureService, $runner->getProcess()->getID());
        }
        return null;
    }

    protected function createConsoleOutput(TaskRunnerInterface $runner, ConsoleOutputInterface $output)
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

    protected function createDashboardOutput(TaskRunnerInterface $runner): \Concrete\Core\Command\Task\Output\OutputInterface
    {
        $processLogger = $this->loggerFactory->createFromRunner($runner);
        $pushOutput = $this->getPushOutput($runner);
        $outputs = [];
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

    public function createDashboardContext(TaskRunnerInterface $runner): ContextInterface
    {
        return $this->app->make(DashboardContext::class, ['output' => $this->createDashboardOutput($runner)]);
    }

    public function createConsoleContext(TaskRunnerInterface $runner, OutputInterface $output): ContextInterface
    {
        return $this->app->make(ConsoleContext::class, ['output' => $this->createConsoleOutput($runner, $output)]);
    }



}

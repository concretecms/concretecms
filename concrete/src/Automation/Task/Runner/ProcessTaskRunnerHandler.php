<?php
namespace Concrete\Core\Automation\Task\Runner;

use Concrete\Core\Automation\Process\ProcessService;
use Concrete\Core\Foundation\Command\AsynchronousBus;
use Concrete\Core\Foundation\Command\DispatcherFactory;

defined('C5_EXECUTE') or die("Access Denied.");

class ProcessTaskRunnerHandler
{

    /**
     * @var DispatcherFactory
     */
    protected $dispatcherFactory;

    /**
     * @var ProcessService
     */
    protected $processService;

    public function __construct(ProcessService $processService, DispatcherFactory $dispatcherFactory)
    {
        $this->processService = $processService;
        $this->dispatcherFactory = $dispatcherFactory;
    }

    public function handle(ProcessTaskRunner $runner)
    {
        $queue = 'default'; // @TODO: Return this from the dispatcher factory.
        $process = $this->processService->createProcess($runner->getTask(), $runner->getInput(), $queue);

        $dispatcher = $this->dispatcherFactory->getDispatcher();
        // @TODO: Simplify all this. I don't think we need these configurable buses. I dont' think we need handles
        // and queues and all of that.
        $dispatcher->dispatch($runner->getCommand(), AsynchronousBus::getHandle());

        $runner->setProcess($process);

    }


}

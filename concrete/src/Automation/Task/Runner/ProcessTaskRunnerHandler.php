<?php
namespace Concrete\Core\Automation\Task\Runner;

use Concrete\Core\Automation\Process\ProcessService;
use Concrete\Core\Foundation\Command\AsynchronousBus;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use Symfony\Component\Messenger\MessageBusInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ProcessTaskRunnerHandler
{

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var ProcessService
     */
    protected $processService;

    public function __construct(ProcessService $processService, MessageBusInterface $messageBus)
    {
        $this->processService = $processService;
        $this->messageBus = $messageBus;
    }

    public function __invoke(ProcessTaskRunner $runner)
    {
        $queue = 'default'; // @TODO: Return this from the dispatcher factory.
        $process = $this->processService->createProcess($runner->getTask(), $runner->getInput(), $queue);

        $this->messageBus->dispatch($runner->getMessage());

        $runner->setProcess($process);

    }


}

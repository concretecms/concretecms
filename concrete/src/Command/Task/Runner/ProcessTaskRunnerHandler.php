<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Process\ProcessService;
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
        $process = $this->processService->createProcess(
            $runner->getTask(),
            $runner->getInput(),
        );

        $this->messageBus->dispatch($runner->getMessage());

        $runner->setProcess($process);

    }


}

<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Process\Command\HandleProcessMessageCommand;
use Concrete\Core\Command\Process\ProcessFactory;
use Symfony\Component\Messenger\MessageBusInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ProcessTaskRunnerHandler
{

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var ProcessFactory
     */
    protected $processFactory;

    public function __construct(ProcessFactory $processFactory, MessageBusInterface $messageBus)
    {
        $this->processFactory = $processFactory;
        $this->messageBus = $messageBus;
    }

    public function __invoke(ProcessTaskRunner $runner)
    {
        $process = $this->processFactory->createTaskProcess($runner->getTask());

        $wrappedMessage = new HandleProcessMessageCommand($process->getID(), $runner->getMessage());

        $this->messageBus->dispatch($wrappedMessage);

        $runner->setProcess($process);
    }


}

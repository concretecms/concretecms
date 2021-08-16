<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Command\Process\Command\ProcessMessageInterface;
use Concrete\Core\Command\Process\ProcessUpdater;
use Concrete\Core\Command\Task\Command\ExecuteConsoleTaskCommand;
use Concrete\Core\Command\Task\Input\InputFactory;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunnerInterface;
use Concrete\Core\Messenger\MessageBusAwareInterface;
use Concrete\Core\Command\Task\Input\Input;
use Concrete\Core\Command\Task\Output\OutputFactory;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Messenger\MessageBusManager;
use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskCommand extends SymfonyCommand
{

    /**
     * @var TaskInterface
     */
    protected $task;

    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
        parent::__construct();
    }

    public function configure()
    {
        $controller = $this->task->getController();
        $this->setName(sprintf('task:%s', $controller->getConsoleCommandName()));
        $this->setDescription($controller->getDescription());

        $definition = $controller->getInputDefinition();
        if ($definition) {
            $definition->addToCommand($this);
        }
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Facade::getFacadeApplication();
        $inputFactory = $app->make(InputFactory::class);

        $taskInput = $inputFactory->createFromConsoleInput($input, $this->task->getController()->getInputDefinition());
        $command = new ExecuteConsoleTaskCommand($this->task, $taskInput, $output);
        $app->executeCommand($command);

        return static::SUCCESS;
    }
}

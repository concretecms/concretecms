<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Messenger\MessageBusAwareInterface;
use Concrete\Core\Command\Task\Input\Input;
use Concrete\Core\Command\Task\Output\OutputFactory;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Messenger\MessageBusManager;
use Concrete\Core\Support\Facade\Facade;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
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
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Facade::getFacadeApplication();
        $input = new Input();
        $outputFactory = $app->make(OutputFactory::class);
        $taskOutput = $outputFactory->createConsoleOutput($output);
        $runner = $this->task->getController()->getTaskRunner($this->task, $input);
        $handler = $app->make($runner->getTaskRunnerHandler());

        if ($handler instanceof MessageBusAwareInterface) {
            $handler->setMessageBus($app->make(MessageBusManager::class)->getBus(MessageBusManager::BUS_DEFAULT_SYNCHRONOUS));
        }

        $handler->start($runner, $taskOutput);
        $handler->run($runner);
        $handler->complete($runner, $taskOutput);

        return self::SUCCESS;
    }
}

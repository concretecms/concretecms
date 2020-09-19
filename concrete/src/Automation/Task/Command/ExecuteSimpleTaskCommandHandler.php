<?php
namespace Concrete\Core\Automation\Task\Command;

use Concrete\Core\Automation\Task\Response\ResponseInterface;
use Concrete\Core\Automation\Task\Response\TaskCompletedResponse;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use Concrete\Core\Foundation\Command\SynchronousBus;

defined('C5_EXECUTE') or die("Access Denied.");

class ExecuteSimpleTaskCommandHandler
{

    /**
     * @var DispatcherFactory
     */
    protected $dispatcherFactory;

    public function __construct(DispatcherFactory $dispatcherFactory)
    {
        $this->dispatcherFactory = $dispatcherFactory;
    }

    public function handle(ExecuteSimpleTaskCommand $command)
    {
        $dispatcher = $this->dispatcherFactory->getDispatcher();
        $dispatcher->dispatch($command->getCommand());
    }


}

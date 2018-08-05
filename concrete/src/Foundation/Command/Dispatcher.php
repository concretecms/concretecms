<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Command\Handler\MethodNameInflector\HandleClassNameWithFallbackInflector;
use League\Tactician\Bernard\QueueCommand;
use League\Tactician\Bernard\QueueMiddleware;
use League\Tactician\CommandBus;
use Concrete\Core\Foundation\Command\Middleware\BatchUpdatingMiddleware;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;

class Dispatcher
{

    const BUS_TYPE_SYNC = 1; // Synchronous command bus
    const BUS_TYPE_ASYNC = 2; // Async/queue bus

    /**
     * @var mixed
     */
    protected $queuableCommands = [];

    /**
     * @var CommandBus[]
     */
    protected $buses;

    /**
     * @var InMemoryLocator
     */
    protected $locator;


    /**
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $defaultQueue;

    /**
     * @return string
     */
    public function getDefaultQueue()
    {
        return $this->defaultQueue;
    }

    /**
     * @param string $defaultQueue
     */
    public function setDefaultQueue($defaultQueue)
    {
        $this->defaultQueue = $defaultQueue;
    }

    /**
     * Dispatcher constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->locator = new InMemoryLocator();
        $this->addBus(self::BUS_TYPE_SYNC, new CommandBus([
            $this->app->make(BatchUpdatingMiddleware::class),
            new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                $this->locator,
                new HandleClassNameWithFallbackInflector()
            )
        ]));
        $this->addBus(self::BUS_TYPE_ASYNC, new CommandBus([new QueueMiddleware($this->app->make('queue/producer'))]));
    }

    public function addBus(int $type, CommandBus $bus)
    {
        $this->buses[$type] = $bus;
    }

    public function getBus(int $type)
    {
        return $this->buses[$type];
    }

    public function getSynchronousBus()
    {
        return $this->getBus(self::BUS_TYPE_SYNC);
    }

    public function getQueueBus()
    {
        return $this->getBus(self::BUS_TYPE_ASYNC);
    }

    protected function registerQueuableCommand($command, $queue)
    {
        if ($queue === true) {
            $queue = $this->getDefaultQueue();
        }
        $this->queuableCommands[$command] = $queue;
    }

    public function registerCommand($handler, $command, $queue = null)
    {
        $this->locator->addHandler($handler, $command);
        if ($queue) {
            $this->registerQueuableCommand($command, $queue);
        }
    }

    /**
     * @param CommandInterface $command
     * @return string
     */
    public function getQueueForCommand(CommandInterface $command)
    {
        $queue = $this->getDefaultQueue();
        foreach($this->queuableCommands as $queueableCommand => $queue)
        {
            if ($command instanceof $queueableCommand) {
                $queue = $queue;
                break;
            }
        }
        return $queue;
    }


    /**
     * Retrieves the bus to dispatch a command onto.
     * @param CommandInterface $command
     * @return array
     */
    public function getBusTypeForCommand(CommandInterface $command)
    {
        $useQueue = null;
        foreach($this->queuableCommands as $queueableCommand => $queue)
        {
            if ($command instanceof $queueableCommand) {
                $useQueue = $queue;
                break;
            }
        }
        if ($useQueue) {
            $type = self::BUS_TYPE_ASYNC;
        } else {
            $type = self::BUS_TYPE_SYNC;
        }

        return [$type, $useQueue];
    }

    public function dispatchOnQueue(CommandInterface $command, $queue)
    {
        $bus = $this->getBus(self::BUS_TYPE_ASYNC);
        $command = new QueueCommand($command, $queue);
        return $bus->handle($command);
    }

    /**
     * Executes a command.
     * @param CommandInterface $command
     * @return mixed
     */
    public function dispatch(CommandInterface $command)
    {
        list($bus, $queue) = $this->getBusTypeForCommand($command);
        $bus = $this->getBus($bus);
        if ($queue) {
            $command = new QueueCommand($command, $queue);
        }
        return $bus->handle($command);
    }
}
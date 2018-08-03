<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Command\Handler\MethodNameInflector\HandleClassNameWithFallbackInflector;
use League\Tactician\Bernard\QueueCommand;
use League\Tactician\Bernard\QueueMiddleware;
use League\Tactician\CommandBus;
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

    public function dispatch(CommandInterface $command)
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
            $bus = $this->getBus(self::BUS_TYPE_ASYNC);
            $command = new QueueCommand($command, $useQueue);
        } else {
            $bus = $this->getBus(self::BUS_TYPE_SYNC);
        }
        return $bus->handle($command);
    }
}
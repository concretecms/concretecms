<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use League\Tactician\Bernard\QueueableCommand;
use League\Tactician\Bernard\QueueCommand;

class Dispatcher
{

    protected $buses = [];

    protected $commands = [];

    /**
     * @var Application
     */
    protected $app;

    /**
     * Dispatcher constructor.
     * @param BusFactory $busFactory
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->addBus($this->app->make(SynchronousBus::class));
        $this->addBus($this->app->make(AsynchronousBus::class));
    }

    public function addBus(BusInterface $bus)
    {
        $this->buses[$bus->getHandle()] = $bus;
    }

    /**
     * Registers a command to be executed by the dispatcher. If a specific bus is passed, the bus with the
     * corresponding handle will execute the command. Otherwise, we will attempt to determine which bus
     * to use based on what type of command is passed.
     * @param $handler
     * @param $command
     * @param null $bus
     */
    public function registerCommand($handler, $command, $bus = null)
    {
        $this->commands[] = [$handler, $command, $bus];
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Inspects the passed command, and returns the proper bus to use for it.
     * @param $command
     * @return BusInterface $bus
     */
    public function getBusForCommand($command)
    {
        foreach($this->commands as $row) {
            $registeredCommand = $row[1];
            $busHandle = $row[2];
            if ($command instanceof $registeredCommand) {
                if ($busHandle) {
                    return $this->buses[$busHandle];
                }
            }
        }

        if ($command instanceof QueueableCommand) {
            return $this->buses[AsynchronousBus::getHandle()];
        } else {
            return $this->buses[SynchronousBus::getHandle()];
        }
    }

    /**
     * Takes a command and return a command that we dispatch. We have this because sometimes the command we pass
     * into the dispatcher isn't the one that actually gets dispatched (because we wrap it in a QueueCommand wrapper
     * for example
     * @param $command
     * @param BusInterface $bus
     * @return mixed
     */
    public function wrapCommandForDispatch($command, BusInterface $bus)
    {
        if ($bus instanceof AsynchronousBusInterface) {
            if ($command instanceof QueueableCommand) {
                // This means the command supports asynchronous execution, so we just return the command.
                return $command;
            } else {
                // otherwise, we wrap the command in QueueableCommand
                $command = new QueueCommand($command, $bus->getQueue());
                return $command;
            }
        } else {
            return $command;
        }
    }

    /**
     * Executes a command. If $onBus is passed, the command will be executed on that bus.
     * @param $command
     * @param string|BusInterface $onBus
     * @return mixed
     */
    public function dispatch($command, $onBus = null)
    {
        if ($onBus) {
            if (!($onBus instanceof BusInterface)) {
                $bus = $this->buses[$onBus];
            } else {
                $bus = $onBus;
            }
        } else {
            $bus = $this->getBusForCommand($command);
        }

        $dispatchCommand = $this->wrapCommandForDispatch($command, $bus);
        $commandBus = $bus->build($this);
        return $commandBus->handle($dispatchCommand);
    }



}
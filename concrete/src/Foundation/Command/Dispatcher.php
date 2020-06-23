<?php

namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use League\Tactician\Bernard\QueueableCommand;
use League\Tactician\Bernard\QueueCommand;
use RuntimeException;

class Dispatcher
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Array keys are the bus handles, array values are the bus instances.
     *
     * @var \Concrete\Core\Foundation\Command\BusInterface[]
     */
    protected $buses = [];

    /**
     * Every array item is an array with [command handler instance, class of the handled commands, bus handle or null].
     *
     * @var array
     */
    protected $commands = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->addBus($this->app->make(SynchronousBus::class));
        $this->addBus($this->app->make(AsynchronousBus::class));
    }

    /**
     * @return $this
     */
    public function addBus(BusInterface $bus): object
    {
        $this->buses[$bus->getHandle()] = $bus;

        return $this;
    }

    /**
     * Registers a command to be executed by the dispatcher. If a specific bus is passed, the bus with the
     * corresponding handle will execute the command. Otherwise, we will attempt to determine which bus
     * to use based on what type of command is passed.
     *
     * @param object $handler the command handler instance
     * @param string $commandCoass the class of the command that can be handled by the handler
     * @param string|null $busHandle the handle command bus to be used
     *
     * @return $this
     */
    public function registerCommand(object $handler, string $commandCoass, ?string $busHandle = null): object
    {
        $this->commands[] = [$handler, $commandCoass, $busHandle === '' ? null : $busHandle];

        return $this;
    }

    /**
     * Get the registered commands.
     * Every array item is an array of [command handler instance, class of the handled commands, , bus handle or null].
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Inspects the passed command, and returns the proper bus to use for it.
     *
     * @throws \RuntimeException if a registered command should be used with a bus that's not registered
     */
    public function getBusForCommand(object $command): BusInterface
    {
        foreach ($this->commands as $row) {
            $registeredCommandClass = $row[1];
            $busHandle = $row[2];
            if ($command instanceof $registeredCommandClass) {
                if ($busHandle !== null) {
                    if (!isset($this->buses[$busHandle])) {
                        throw new RuntimeException(t('The command bus with handle %s is not registered.', $busHandle));
                    }

                    return $this->buses[$busHandle];
                }
            }
        }

        if ($command instanceof QueueableCommand) {
            return $this->buses[AsynchronousBus::getHandle()];
        }

        return $this->buses[SynchronousBus::getHandle()];
    }

    /**
     * Takes a command and return a command that we dispatch. We have this because sometimes the command we pass
     * into the dispatcher isn't the one that actually gets dispatched (because we wrap it in a QueueCommand wrapper
     * for example).
     */
    public function wrapCommandForDispatch(object $command, BusInterface $bus): object
    {
        if ($bus instanceof AsynchronousBusInterface) {
            if ($command instanceof QueueableCommand) {
                // This means the command supports asynchronous execution, so we just return the command.
                return $command;
            }
            // otherwise, we wrap the command in QueueableCommand
            return new QueueCommand($command, $bus->getQueue());
        }

        return $command;
    }

    /**
     * Executes a command. If $onBus is passed, the command will be executed on that bus.
     *
     * @param string|\Concrete\Core\Foundation\Command\BusInterface|null $onBus
     *
     * @throws \RuntimeException if $onBus is a the handle of an unregistered bus
     *
     * @return mixed the output of the bus handle method
     */
    public function dispatch(object $command, $onBus = null)
    {
        if ($onBus) {
            if ($onBus instanceof BusInterface) {
                $bus = $onBus;
            } else {
                if (!isset($this->buses[$onBus])) {
                    throw new RuntimeException(t('The command bus with handle %s is not registered.', $onBus));
                }
                $bus = $this->buses[$onBus];
            }
        } else {
            $bus = $this->getBusForCommand($command);
        }

        $dispatchCommand = $this->wrapCommandForDispatch($command, $bus);
        $commandBus = $bus->build($this);

        return $commandBus->handle($dispatchCommand);
    }
}

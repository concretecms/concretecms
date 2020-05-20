<?php

namespace Concrete\Core\Foundation\Command\Handler;

use Concrete\Core\Application\Application;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\InMemoryLocator;

class ApplicationAwareLocator extends InMemoryLocator
{

    /**
     * @var \Concrete\Core\Application\Application
     */
    private $app;

    public function __construct(Application $app, array $commandClassToHandlerMap = [])
    {
        parent::__construct($commandClassToHandlerMap);
        $this->app = $app;
    }

    /**
     * Returns the handler bound to the command's class name.
     *
     * @param string $commandName
     *
     * @return object
     */
    public function getHandlerForCommand($commandName)
    {
        if (!isset($this->handlers[$commandName])) {
            throw MissingHandlerException::forCommand($commandName);
        }

        $handler = $this->handlers[$commandName];

        // If we have a string, try to make it with the container
        if (is_string($handler)) {
            $handler = $this->app->make($handler);
        }

        // If we are given a callable, try calling it to get the handler
        if (is_callable($handler)) {
            $handler = $this->app->call($handler);
        }

        // Reset the handler in case our resolution had an effect
        $this->handlers[$commandName] = $handler;

        return $handler;
    }

}

<?php

namespace Concrete\Core\Foundation\Command\Handler;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Foundation\Command\HandlerAwareCommandInterface;
use Concrete\Core\Foundation\Command\SelfHandlingCommandInterface;
use League\Tactician\Exception\CanNotInvokeHandlerException;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\Locator\InMemoryLocator;

/**
 * A class responsible for handling Concrete-specific command locating, including the ability to hydrate
 * via Application, and self handling commands.
 */
class CoreLocator implements HandlerLocator
{

    /**
     * @var \Concrete\Core\Config\Repository\Repository;
     */
    private $config;

    /**
     * @var \Concrete\Core\Application\Application
     */
    private $app;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
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
        $handler = null;

        $commands = $this->config->get('app.commands');
        foreach($commands as $entry) {
            if ($entry[0] === $commandName) {
                $handler = $entry[1];
            }
        }

        if (!$handler) {
            $reflectionCommand = new \ReflectionClass($commandName);
            if ($reflectionCommand->implementsInterface(HandlerAwareCommandInterface::class)) {
                $handler = $commandName::getHandler();
            }
        }

        if (!$handler) {
            throw MissingHandlerException::forCommand($commandName);
        }

        // If we have a string, try to make it with the container
        if (is_string($handler)) {
            $handler = $this->app->make($handler);
        }

        // If we are given a callable, try calling it to get the handler
        if (is_callable($handler)) {
            $handler = $this->app->call($handler);
        }

        return $handler;
    }

}

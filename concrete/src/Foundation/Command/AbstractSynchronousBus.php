<?php

namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Command\Handler\ApplicationAwareLocator;
use Concrete\Core\Foundation\Command\Handler\MethodNameInflector\HandleClassNameWithFallbackInflector;
use Concrete\Core\Foundation\Command\Middleware\BatchUpdatingMiddleware;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;

abstract class AbstractSynchronousBus implements SynchronousBusInterface
{
    use MiddlewareManagerTrait;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Build a command bus that submits synchronously.
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Command\BusInterface::build()
     */
    public function build(Dispatcher $dispatcher): CommandBus
    {
        $middleware = array_merge($this->getMiddleware(), $this->getRequiredMiddleware($dispatcher));

        return $this->app->make(CommandBus::class, ['middleware' => $middleware]);
    }

    /**
     * @return \League\Tactician\Middleware[]
     */
    protected function getRequiredMiddleware(Dispatcher $dispatcher): array
    {
        $locator = $this->app->make(ApplicationAwareLocator::class);
        foreach ($dispatcher->getCommands() as [$handler, $commandClass]) {
            $locator->addHandler($handler, $commandClass);
        }

        return [
            $this->app->make(BatchUpdatingMiddleware::class),
            new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                $locator,
                new HandleClassNameWithFallbackInflector()
            ),
        ];
    }
}

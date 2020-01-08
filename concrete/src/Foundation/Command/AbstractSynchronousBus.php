<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Command\Handler\ApplicationAwareLocator;
use Concrete\Core\Foundation\Command\Handler\MethodNameInflector\HandleClassNameWithFallbackInflector;
use Concrete\Core\Foundation\Command\Middleware\BatchUpdatingMiddleware;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Middleware;

abstract class AbstractSynchronousBus implements SynchronousBusInterface
{

    use MiddlewareManagerTrait;

    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return Middleware[]
     */
    protected function getRequiredMiddleware(Dispatcher $dispatcher)
    {
        $locator = $this->app->make(ApplicationAwareLocator::class);
        foreach($dispatcher->getCommands() as $row) {
            [$handler, $command] = $row;
            $locator->addHandler($handler, $command);
        }

        $middleware = [
            $this->app->make(BatchUpdatingMiddleware::class),
            new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                $locator,
                new HandleClassNameWithFallbackInflector()
            )
        ];

        return $middleware;
    }

    /**
     * Build a command bus that submits synchronously
     *
     * @param \Concrete\Core\Foundation\Command\Dispatcher $dispatcher
     *
     * @return \League\Tactician\CommandBus
     */
    public function build(Dispatcher $dispatcher)
    {
        $middleware = array_merge($this->getMiddleware(), $this->getRequiredMiddleware($dispatcher));
        return $this->app->make(CommandBus::class, ['middleware' => $middleware]);
    }
}

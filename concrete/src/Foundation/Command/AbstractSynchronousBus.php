<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\Command\Handler\MethodNameInflector\HandleClassNameWithFallbackInflector;
use Concrete\Core\Foundation\Command\Middleware\BatchUpdatingMiddleware;
use Illuminate\Config\Repository;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Middleware;

abstract class AbstractSynchronousBus implements SynchronousBusInterface
{

    /**
     * @return Middleware[]
     */
    public function getMiddleware()
    {
        return [];
    }

    /**
     * @return Middleware[]
     */
    protected function getRequiredMiddleware(Dispatcher $dispatcher)
    {
        $locator = new InMemoryLocator();
        foreach($dispatcher->getCommands() as $row) {
            $handler = $row[0];
            $command = $row[1];
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
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function build(Dispatcher $dispatcher)
    {
        $middleware = array_merge($this->getMiddleware(), $this->getRequiredMiddleware($dispatcher));
        return new CommandBus($middleware);
    }
}

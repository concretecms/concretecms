<?php

namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use League\Tactician\Bernard\QueueMiddleware;
use League\Tactician\CommandBus;

class AsynchronousBus implements AsynchronousBusInterface
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
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Command\BusInterface::getHandle()
     */
    public static function getHandle(): string
    {
        return 'core_async';
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Command\AsynchronousBusInterface::getQueue()
     */
    public function getQueue(): string
    {
        return 'default';
    }

    /**
     * Build a command bus that ultimately submits all sent commands asynchronously.
     *
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Command\BusInterface::build()
     */
    public function build(Dispatcher $dispatcher): CommandBus
    {
        $middlewares = $this->getMiddleware();
        $middlewares[] = $this->app->make(QueueMiddleware::class, [
            'producer' => $this->app->make('queue/producer'),
        ]);

        return $this->app->make(CommandBus::class, ['middleware' => $middlewares]);
    }
}

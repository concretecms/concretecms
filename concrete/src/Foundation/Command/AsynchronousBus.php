<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use League\Tactician\Bernard\QueueMiddleware;
use League\Tactician\CommandBus;

class AsynchronousBus implements AsynchronousBusInterface
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

    public function getQueue()
    {
        return 'default';
    }

    public static function getHandle()
    {
        return 'core_async';
    }

    /**
     * Build a command bus that ultimately submits all sent commands asynchronously
     *
     * @param \Concrete\Core\Foundation\Command\Dispatcher $dispatcher
     *
     * @return \League\Tactician\CommandBus
     */
    public function build(Dispatcher $dispatcher)
    {
        $middlewares = $this->getMiddleware();
        $middlewares[] = $this->app->make(QueueMiddleware::class, [
            'producer' => $this->app->make('queue/producer')
        ]);

        return $this->app->make(CommandBus::class, ['middleware' => $middlewares]);
    }
}

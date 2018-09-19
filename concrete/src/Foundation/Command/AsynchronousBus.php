<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\ErrorList\ErrorList;
use Illuminate\Config\Repository;
use League\Tactician\Bernard\QueueMiddleware;
use League\Tactician\CommandBus;

class AsynchronousBus implements AsynchronousBusInterface
{

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

    public function build(Dispatcher $dispatcher)
    {
        return new CommandBus(
            [new QueueMiddleware(
                $this->app->make('queue/producer')
            )]
        );
    }
}

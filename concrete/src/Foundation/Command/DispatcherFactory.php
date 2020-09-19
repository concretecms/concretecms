<?php

namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use Illuminate\Config\Repository;

class DispatcherFactory
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Foundation\Command\Dispatcher
     */
    protected $dispatcher;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->dispatcher = $app->make(Dispatcher::class);
    }

    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }
}

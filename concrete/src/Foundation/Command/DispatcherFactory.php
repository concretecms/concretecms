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
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @var \Concrete\Core\Foundation\Command\Dispatcher
     */
    protected $dispatcher;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->dispatcher = $app->make(Dispatcher::class);
        foreach ($this->config->get('app.commands') as $entry) {
            $command = $entry[0];
            $handler = $entry[1];
            $bus = $entry[2] ?? null;
            $this->dispatcher->registerCommand($this->app->make($handler), $command, $bus);
        }
    }

    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }
}

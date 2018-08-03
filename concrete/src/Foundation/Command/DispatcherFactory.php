<?php
namespace Concrete\Core\Foundation\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\ErrorList\ErrorList;
use Illuminate\Config\Repository;

class DispatcherFactory
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Repository
     */
    protected $config;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    public function createDispatcher()
    {
        $dispatcher = new Dispatcher($this->app);
        $dispatcher->setDefaultQueue($this->config->get('concrete.queue.default'));
        foreach($this->config->get('app.commands') as $entry) {
            $command = $entry[0];
            $handler = $entry[1];
            $queue = $entry[2];
            $dispatcher->registerCommand($this->app->make($handler), $command, $queue);
        }
        return $dispatcher;
    }

}

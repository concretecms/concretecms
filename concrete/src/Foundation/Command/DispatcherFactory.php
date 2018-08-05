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

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(Application $app, Repository $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->dispatcher = new Dispatcher($this->app);
        $defaultQueue = $this->config->get('concrete.queue.default');
        $this->dispatcher->setDefaultQueue($defaultQueue);
        foreach($this->config->get('app.commands') as $entry) {
            $command = $entry[0];
            $handler = $entry[1];
            $queue = null;
            if (isset($entry[2])) {
                $queue = $entry[2];
            }
            $this->dispatcher->registerCommand($this->app->make($handler), $command, $queue);
        }
    }

    public function registerCommand($handler, $command, $queue = null)
    {
        $this->dispatcher->registerCommand($handler, $command, $queue);
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

}

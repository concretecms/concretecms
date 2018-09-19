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
        $this->dispatcher = $app->make(Dispatcher::class);
        foreach($this->config->get('app.commands') as $entry) {
            $command = $entry[0];
            $handler = $entry[1];
            $bus = null;
            if (isset($entry[2])) {
                $bus = $entry[2];
            }
            $this->dispatcher->registerCommand($this->app->make($handler), $command, $bus);
        }
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }

}

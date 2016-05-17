<?php
namespace Concrete\Core\Foundation\Runtime\Run;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Console\Application as ConsoleApplication;

class CLIRunner implements RunInterface, ApplicationAwareInterface
{
    /** @var Application */
    protected $app;

    /** @var ConsoleApplication */
    protected $console;

    public function __construct(ConsoleApplication $console)
    {
        $this->console = $console;
    }

    /**
     * Run the runtime.
     *
     * @return mixed
     */
    public function run()
    {
        $console = $this->console;
        $this->app->instance('console', $console);

        if ($this->app->isInstalled()) {
            $this->app->setupPackageAutoloaders();
            $this->app->setupPackages();
        }

        $console->setupDefaultCommands();

        \Events::dispatch('on_before_console_run');

        $console->run();

        \Events::dispatch('on_after_console_run');
    }

    /**
     * Set the application object.
     *
     * @param \Concrete\Core\Application\Application $application
     */
    public function setApplication(Application $application)
    {
        $this->app = $application;
    }
}

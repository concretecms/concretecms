<?php
namespace Concrete\Core\Foundation\Runtime\Run;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\ArgvInput;

class CLIRunner implements RunInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /** @var ConsoleApplication */
    protected $console;

    public function __construct(ConsoleApplication $console)
    {
        $this->console = $console;
    }

    private function loadBootstrap()
    {
        $app = $this->app;
        $console = $this->console;
        include DIR_APPLICATION . '/bootstrap/app.php';
    }

    private function initializeSystemTimezone()
    {
        $config = $this->app->make('config');
        if (!$config->has('app.server_timezone')) {
            // There is no server timezone set.
            $config->set('app.server_timezone', @date_default_timezone_get() ?: 'UTC');
        }
        @date_default_timezone_set($config->get('app.server_timezone'));
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

        $this->loadBootstrap();
        $this->initializeSystemTimezone();

        $input = new ArgvInput();
        if ($input->getFirstArgument() !== 'c5:update') {
            if ($this->app->isInstalled()) {
                $this->app->setupPackageAutoloaders();
                $this->app->setupPackages();
            }
        }

        $console->setupDefaultCommands();

        \Events::dispatch('on_before_console_run');

        $console->run($input);

        \Events::dispatch('on_after_console_run');
    }
}

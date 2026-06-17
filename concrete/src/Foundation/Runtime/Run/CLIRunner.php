<?php
namespace Concrete\Core\Foundation\Runtime\Run;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Console\Application as ConsoleApplication;
use Concrete\Core\Console\CommandRegistry;
use Concrete\Core\Console\OutputStyle;
use Concrete\Core\Install\Preconditions\MemoryLimit;
use Concrete\Core\Utility\Service\Number;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

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

    private function testAvailableMemory(InputInterface $input, OutputInterface $output)
    {
        $number = new Number();
        $recommended = $number->getBytes(MemoryLimit::MINIMUM_RECOMMENDED_MEMORY);
        $memoryLimit = ini_get('memory_limit');
        if (empty($memoryLimit) || $memoryLimit == -1) {
            $memoryLimit = null;
        } else {
            $memoryLimit = $number->getBytes($memoryLimit);
        }
        if ($memoryLimit !== null && $memoryLimit < $recommended) {
            $style = new OutputStyle($input, $output);
            $style->warning(t('Concrete runs best with at least %1$s of RAM. Your memory limit is currently %2$s. You may experience silent failures or unexpected premature exits when running console commands.',
                $number->formatSize($recommended),
                $number->formatSize($memoryLimit)
            ));
        }
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
        if ($this->app->isInstalled()) {
            $this->app->setupPackageAutoloaders();
            if ($input->getFirstArgument() !== 'c5:update') {
                // In GitHub issue #5640 we gated this functionality completely, including autoloaders.
                // But this causes issues when upgrading with addons that define their own permission keys.
                // I'm still not convinced that disabling ANYTHING on update is a good policy, but for now
                // let's run setupPackageAutoloaders always, but not run setupPackages (which is on_start())
                $this->app->setupPackages();
            }
        }

        $registry = new CommandRegistry($this->console);
        $registry->setApplication($this->app);
        $registry->registerCommands();

        if ($this->shouldRunCommands() === false) {
            return;
        }

        // Load the console commands

        \Events::dispatch('on_before_console_run');

        $output = new ConsoleOutput();
        $this->testAvailableMemory($input, $output);

        $console->run($input, $output);

        \Events::dispatch('on_after_console_run');
    }

    protected function shouldRunCommands(): bool
    {
        return defined('C5_ENVIRONMENT_ONLY') && C5_ENVIRONMENT_ONLY ? false : true;
    }
}

<?php

namespace Concrete\Core\Console;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Tools\Console\Doctrine\ConsoleRunner;
use Concrete\Core\Updater\Migrations\Configuration as MigrationsConfiguration;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class ServiceProvider extends Provider
{

    /** @var \Concrete\Core\Console\Application */
    protected $cli;

    /** @var bool */
    protected $installed;

    /**
     * Commands that are always available
     *
     * @var string[]
     */
    protected $commands = [
        Command\InfoCommand::class,
        Command\InstallCommand::class,
        Command\InstallLanguageCommand::class,
        Command\TranslatePackageCommand::class,
        Command\GenerateIDESymbolsCommand::class,
        Command\ConfigCommand::class,
        Command\PackPackageCommand::class,
        Command\ExecCommand::class,
        Command\ServiceCommand::class,
        Command\ResetCommand::class,
    ];

    /**
     * Commands that only get added when concrete5 is installed
     *
     * @var string[]
     */
    protected $installedCommands = [
        Command\CompareSchemaCommand::class,
        Command\ClearCacheCommand::class,
        Command\InstallPackageCommand::class,
        Command\UninstallPackageCommand::class,
        Command\UpdatePackageCommand::class,
        Command\BlacklistClear::class,
        Command\JobCommand::class,
        Command\UpdateCommand::class,
    ];

    /**
     * The commands used for migration. These get an extra MigrationConfiguration object
     *
     * @var string[]
     */
    protected $migrationCommands = [
        \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand::class,
        \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand::class,
        \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand::class,
        \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand::class,
        \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand::class,
        \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand::class,
    ];

    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        $this->app->extend(Application::class, function (Application $cli) {
            $this->cli = $cli;
            $this->setupDefaultCommands();
            $this->setupDoctrineCommands();

            return $cli;
        });
    }

    protected function setupDefaultCommands()
    {
        // Add commands that always work
        $this->add($this->commands);

        // Add commands that require install to work
        $this->add($this->installedCommands, true);
    }

    public function setupDoctrineCommands()
    {
        if ($this->installed()) {
            // Set the doctrine helperset to the CLI
            $this->cli->setHelperSet($this->app->call([ConsoleRunner::class, 'createHelperSet']));

            // Add Doctrine ConsoleRunner commands
            ConsoleRunner::addCommands($this->cli);

            // Add migration commands
            $migrationsConfiguration = $this->getMigrationConfiguration();
            $this->add($this->migrationCommands, true, function (AbstractCommand $command) use ($migrationsConfiguration) {
                // Set the migration configuration
                $command->setMigrationConfiguration($migrationsConfiguration);
            });
        }
    }

    /**
     * Add a class to the CLI application
     * @param $param
     * @return null|\Symfony\Component\Console\Command\Command
     */
    private function add($param, $requireInstall = false, callable $callback = null)
    {
        // Handle array input
        if (is_array($param)) {
            foreach ($param as $item) {
                $this->add($item, $requireInstall, $callback);
            }

            return null;
        }

        // If we're not installed, only register commands that are marked to handle that
        if ($requireInstall && !$this->installed()) {
            return null;
        }

        // Inflate the passed command
        $command = is_string($param) ? $this->app->make($param) : $param;

        // Make sure we have a command instance
        if (!$command || !$command instanceof SymfonyCommand) {
            throw new \InvalidArgumentException('Invalid command provided.');
        }

        // Handle callback function
        if ($callback) {
            $command = $callback($command);

            if (!$command) {
                return null;
            }
        }

        // Add the cli command
        return $this->cli->add($command);
    }

    /**
     * Determine if the app is currently installed
     *
     * @return bool
     */
    private function installed()
    {
        if ($this->installed === null) {
            $this->installed = $this->app->isInstalled();
        }

        return $this->installed;
    }

    /**
     * @return \Concrete\Core\Updater\Migrations\Configuration|mixed
     */
    private function getMigrationConfiguration()
    {
        $migrationsConfiguration = $this->app->make(MigrationsConfiguration::class);
        $output = $this->app->make(ConsoleOutput::class);
        $migrationsConfiguration->setOutputWriter(new OutputWriter(function ($message) use ($output) {
            $output->writeln($message);
        }));
        return $migrationsConfiguration;
    }
}

<?php

namespace Concrete\Core\Console;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Tools\Console\Doctrine\ConsoleRunner as DeprecatedConsoleRunner;
use Concrete\Core\Updater\Migrations\Configuration as MigrationsConfiguration;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Output\ConsoleOutput;

class ServiceProvider extends Provider
{
    /** @var \Concrete\Core\Console\Application */
    protected $cli;

    /** @var bool */
    protected $installed;

    /**
     * Commands that are always available.
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
        Command\PhpCodingStyleCommand::class,
    ];

    /**
     * Commands that only get added when concrete5 is installed.
     *
     * @var string[]
     */
    protected $installedCommands = [
        Command\CompareSchemaCommand::class,
        Command\ClearCacheCommand::class,
        Command\InstallPackageCommand::class,
        Command\UninstallPackageCommand::class,
        Command\UpdatePackageCommand::class,
        Command\InstallThemeCommand::class,
        Command\BlacklistClear::class,
        Command\JobCommand::class,
        Command\RefreshEntitiesCommand::class,
        Command\GenerateSitemapCommand::class,
        Command\FillThumbnailsTableCommand::class,
        Command\RescanFilesCommand::class,
        Command\UpdateCommand::class,
        Command\SetDatabaseCharacterSetCommand::class,
        Command\Express\ExportCommand::class,
        Command\FixDatabaseForeignKeys::class,
    ];

    /**
     * The commands used for migration. These get an extra MigrationConfiguration object.
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
        /*
         * @deprecated Use the final ConsoleRunner class directly
         * @todo Remove in v9
         */
        if (!class_exists(DeprecatedConsoleRunner::class)) {
            class_alias(ConsoleRunner::class, DeprecatedConsoleRunner::class);
        }

        $this->app->extend(Application::class, function (Application $cli) {
            $this->cli = $cli;
            $this->setupDefaultCommands();
            $this->setupDoctrineCommands();

            return $cli;
        });
    }

    public function setupDoctrineCommands()
    {
        if ($this->installed()) {
            // Set the doctrine helperset to the CLI
            $doctrineHelperSet = $this->app->call([ConsoleRunner::class, 'createHelperSet']);
            if ($this->cli->getHelperSet()) {
                foreach ($doctrineHelperSet as $key => $helper) {
                    $this->cli->getHelperSet()->set($helper, $key);
                }
            } else {
                $this->cli->setHelperSet($doctrineHelperSet);
            }

            // Add Doctrine ConsoleRunner commands
            ConsoleRunner::addCommands($this->cli);

            // Add migration commands
            $migrationsConfiguration = $this->getMigrationConfiguration();

            foreach ($this->migrationCommands as $migrationsCommand) {
                $command = $this->app->make($migrationsCommand);
                $command->setMigrationConfiguration($migrationsConfiguration);
                $this->add($command, true);
            }
        }
    }

    protected function setupDefaultCommands()
    {
        // Add commands that always work
        $this->add($this->commands);

        // Add commands that require install to work
        $this->add($this->installedCommands, true);
    }

    /**
     * Add a class to the CLI application.
     *
     * @param array|string|\Symfony\Component\Console\Command\Command $param
     * @param bool $requireInstall
     * @param callable|null $callback
     *
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
     * Determine if the app is currently installed.
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

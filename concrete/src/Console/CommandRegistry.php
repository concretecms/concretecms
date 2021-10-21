<?php

namespace Concrete\Core\Console;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Console\Command\TaskCommand;
use Concrete\Core\Updater\Migrations\Configuration as MigrationsConfiguration;
use Doctrine\Migrations\OutputWriter;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;

/**
 * Adds commands to the console
 *
 * Important note: this used to be a part of the Console service provider (which has been removed). We had to remove it
 * because it fired too early. Packages could not modify the dependencies passed to the command classes because they
 * fired after the commands were registered. This is simplified and provides exactly the same functionality.
 */
class CommandRegistry implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /**
     * @var Application
     */
    protected $console;

    /**
     * Commands that are always available.
     *
     * @var string[]
     */
    protected $commands = [
        Command\InfoCommand::class,
        Command\InstallCommand::class,
        Command\IsInstalledCommand::class,
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
     * Commands that only get added when Concrete is installed.
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
        Command\DenylistClear::class,
        Command\JobCommand::class,
        Command\RefreshEntitiesCommand::class,
        Command\GenerateSitemapCommand::class,
        Command\RescanFilesCommand::class,
        Command\UpdateCommand::class,
        Command\SetDatabaseCharacterSetCommand::class,
        Command\RefreshBoardsCommand::class,
        Command\RunSchedulerCommand::class,
        Command\RunSchedulerInForegroundCommand::class,
        Command\Express\ExportCommand::class,
        Command\FixDatabaseForeignKeys::class,
        Command\ReindexCommand::class,
        Command\GenerateFileIdentifiersCommand::class,
        ConsumeMessagesCommand::class,

        /*
        MessengerCommand\FailedMessagesShowCommand::class,
        MessengerCommand\FailedMessagesRetryCommand::class,
        MessengerCommand\FailedMessagesRemoveCommand::class,
        MessengerCommand\SetupTransportsCommand::class,
        MessengerCommand\StopWorkersCommand::class,
        MessengerCommand\ConsumeMessagesCommand::class,
        */

        Command\BulkUserAssignCommand::class
    ];

    /**
     * The commands used for migration. These get an extra MigrationConfiguration object.
     *
     * @var string[]
     */
    protected $migrationCommands = [
        \Doctrine\Migrations\Tools\Console\Command\DiffCommand::class,
        \Doctrine\Migrations\Tools\Console\Command\ExecuteCommand::class,
        \Doctrine\Migrations\Tools\Console\Command\GenerateCommand::class,
        \Doctrine\Migrations\Tools\Console\Command\MigrateCommand::class,
        \Doctrine\Migrations\Tools\Console\Command\StatusCommand::class,
        \Doctrine\Migrations\Tools\Console\Command\VersionCommand::class,
    ];

    /**
     * CommandRegistry constructor.
     * @param Application $console
     */
    public function __construct(Application $console)
    {
        $this->console = $console;
    }

    public function registerCommands()
    {
        $this->setupDefaultCommands();
        $this->setupInstalledCommands();
        $this->setupDoctrineCommands();
        $this->setupTaskCommands();
    }

    protected function setupDefaultCommands()
    {
        // Add commands that always work
        foreach($this->commands as $commandClass) {
            $this->console->add($this->app->make($commandClass));
        }
    }

    public function setupInstalledCommands()
    {
        if ($this->app->isInstalled()) {
            foreach ($this->installedCommands as $commandClass) {
                $this->console->add($this->app->make($commandClass));
            }
        }
    }

    public function setupDoctrineCommands()
    {
        if ($this->app->isInstalled()) {
            // Set the doctrine helperset to the CLI
            $doctrineHelperSet = $this->app->call([ConsoleRunner::class, 'createHelperSet']);
            if ($this->console->getHelperSet()) {
                foreach ($doctrineHelperSet as $key => $helper) {
                    $this->console->getHelperSet()->set($helper, $key);
                }
            } else {
                $this->console->setHelperSet($doctrineHelperSet);
            }

            // Add Doctrine ConsoleRunner commands
            ConsoleRunner::addCommands($this->console);

            // Add migration commands
            $migrationsConfiguration = $this->getMigrationConfiguration();

            foreach ($this->migrationCommands as $migrationsCommand) {
                $command = $this->app->make($migrationsCommand);
                $command->setMigrationConfiguration($migrationsConfiguration);
                $this->console->add($command);
            }
        }
    }

    protected function setupTaskCommands()
    {
        if ($this->app->isInstalled()) {
            try {
                $tasks = $this->app->make(TaskService::class)->getList();
                foreach($tasks as $task) {
                    $this->console->add(new TaskCommand($task));
                }
            } catch (\Exception $e) {
                // Something isn't right. Probably the proxies aren't built yet or are in the process of being rebuilt?
                // Don't let us use task commands unless the proxies are present.
            }
        }
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

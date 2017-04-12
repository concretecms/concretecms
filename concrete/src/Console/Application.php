<?php
namespace Concrete\Core\Console;

use Concrete\Core\Application\Application as CMSApplication;
use Concrete\Core\Updater\Migrations\Configuration as MigrationsConfiguration;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Output\ConsoleOutput;

class Application extends SymfonyApplication
{
    /**
     * @var CMSApplication
     */
    protected $app;

    public function __construct(CMSApplication $app)
    {
        $this->app = $app;
        parent::__construct('concrete5', $this->app->make('config')->get('concrete.version'));
    }

    public function setupDefaultCommands()
    {
        $this->add(new Command\InfoCommand());
        $this->add(new Command\InstallCommand());
        $this->add(new Command\InstallLanguageCommand());
        $this->add(new Command\TranslatePackageCommand());
        $this->add(new Command\GenerateIDESymbolsCommand());
        $this->add(new Command\ConfigCommand());
        $this->add(new Command\PackPackageCommand());
        $this->add(new Command\ExecCommand());
        $this->add(new Command\ServiceCommand());
        if ($this->app->isInstalled()) {
            $this->add(new Command\CompareSchemaCommand());
            $this->add(new Command\ClearCacheCommand());
            $this->add(new Command\InstallPackageCommand());
            $this->add(new Command\UninstallPackageCommand());
            $this->add(new Command\UpdatePackageCommand());
        }
        $this->setupRestrictedCommands();
        $this->setupDoctrineCommands();
    }

    public function setupRestrictedCommands()
    {
        $this->add(new Command\ResetCommand());
        if ($this->app->isInstalled()) {
            $this->add(new Command\JobCommand());
            $this->add(new Command\UpdateCommand());
        }
    }

    public function setupDoctrineCommands()
    {
        if ($this->app->isInstalled()) {
            $helperSet = ConsoleRunner::createHelperSet(\ORM::entityManager());
            $this->setHelperSet($helperSet);

            $migrationsConfiguration = new MigrationsConfiguration();
            $output = new ConsoleOutput();
            $migrationsConfiguration->setOutputWriter(new OutputWriter(function ($message) use ($output) {
                $output->writeln($message);
            }));

            /** @var \Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand[] $commands */
            $commands = [
                new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
                new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
                new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
                new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
                new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
                new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),
            ];

            foreach ($commands as $migrationsCommand) {
                $migrationsCommand->setMigrationConfiguration($migrationsConfiguration);
                $this->add($migrationsCommand);
            }

            ConsoleRunner::addCommands($this);
        }
    }
}

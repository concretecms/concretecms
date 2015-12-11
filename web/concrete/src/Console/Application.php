<?php

namespace Concrete\Core\Console;

use Core;
use Database;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Concrete\Core\Updater\Migrations\Configuration as MigrationsConfiguration;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct('concrete5', \Config::get('concrete.version'));
    }

    public function setupDefaultCommands()
    {
        $this->add(new Command\ConfigCommand());
        $this->add(new Command\InstallCommand());
        $this->add(new Command\ClearCacheCommand());
        $this->add(new Command\GenerateIDESymbolsCommand());
        $this->add(new Command\InstallPackageCommand());
        $this->add(new Command\UninstallPackageCommand());
        $this->add(new Command\UpdatePackageCommand());
        $this->add(new Command\TranslatePackageCommand());
        $this->setupRestrictedCommands();
        $this->setupDoctrineCommands();
    }

    public function setupRestrictedCommands()
    {
        $this->add(new Command\ResetCommand());
        $this->add(new Command\JobCommand());
    }

    public function setupDoctrineCommands()
    {
        if (!Core::make('app')->isInstalled()) {
            return;
        }
        $cn = Database::connection();
        $helperSet = ConsoleRunner::createHelperSet($cn->getEntityManager());
        $this->setHelperSet($helperSet);

        $migrationsConfiguration = new MigrationsConfiguration();

        /** @var \Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand[] $commands */
        $commands = array(
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),
        );

        foreach ($commands as $migrationsCommand) {
            $migrationsCommand->setMigrationConfiguration($migrationsConfiguration);
            $this->add($migrationsCommand);
        }

        ConsoleRunner::addCommands($this);
    }
}

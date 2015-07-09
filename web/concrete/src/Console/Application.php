<?php

namespace Concrete\Core\Console;

use Config;
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
        $this->add(new Command\ResetCommand());
        $this->add(new Command\InstallCommand());
        $this->add(new Command\GenerateIDESymbolsCommand());
        $this->add(new Command\JobCommand());
        $cn = Database::get();
        /* @var $cn \Concrete\Core\Database\Connection\Connection */
        $helperSet = ConsoleRunner::createHelperSet($cn->getEntityManager());
        $this->setHelperSet($helperSet);
        $mirationsConfiguration = new MigrationsConfiguration();
        foreach (array(
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),
        ) as $migrationsCommand) {
            $migrationsCommand->setMigrationConfiguration($mirationsConfiguration);
            $this->add($migrationsCommand);
        }
        ConsoleRunner::addCommands($this);
    }
}

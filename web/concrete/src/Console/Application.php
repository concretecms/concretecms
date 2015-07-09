<?php

namespace Concrete\Core\Console;

use Config;
use Core;
use Database;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Concrete\Core\Updater\Migrations\Configuration as MigrationsConfiguration;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Package;

class Application extends \Symfony\Component\Console\Application
{
    public function __construct()
    {
        parent::__construct('concrete5', \Config::get('concrete.version'));
        $this->add(new Command\ResetCommand());
        $this->add(new Command\InstallCommand());
        $this->add(new Command\GenerateIDESymbolsCommand());
        $cms = Core::make('app');
        if ($cms->isInstalled()) {
            $cms->setupPackages();
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
    
    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        $cms = Core::make('app');
        
        if ($cms->isInstalled()) {
            $pla = \Concrete\Core\Package\PackageList::get();
            $pl = $pla->getPackages();
            /** @var \Package[] $pl */
            foreach ($pl as $p) {
                if ($p->isPackageInstalled()) {
                    $pkg = Package::getClass($p->getPackageHandle());
                    if (method_exists($pkg, 'getCommands')) {
                        $commands = $pkg->getCommands();
                        foreach ($commands as $command) {
                            $this->add($command);
                        }
                    }
                }
            }
        }
        
        parent::run($input, $output);
    }
}

<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Database\DatabaseStructureManager;
use Concrete\Core\Package\Event\PackageEntities;
use Concrete\Core\Support\Facade\Application;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RefreshEntitiesCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;

        $this
            ->setName('c5:entities:refresh')
            ->setDescription('Refresh the Doctrine database entities')
            ->addEnvOption()
            ->setCanRunAsRoot(false)
            ->setHelp(<<<EOT
This command will refresh the Doctrine database entities and set the following return codes

  0 operation completed succesfully
  {$errExitCode} errors occurred
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();

        $pev = new PackageEntities();
        $app->make(EventDispatcherInterface::class)->dispatch('on_refresh_package_entities', $pev);
        $entityManagers = array_merge([$app->make(EntityManagerInterface::class)], $pev->getEntityManagers());
        foreach ($entityManagers as $em) {
            $manager = new DatabaseStructureManager($em);
            $manager->refreshEntities();
        }

        if ($output->getVerbosity() >= $output::VERBOSITY_NORMAL) {
            $output->writeln('Doctrine cache cleared, proxy classes regenerated, entity database table schema updated.');
        }

        return 0;
    }
}

<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\System\Mutex\MutexInterface;
use Concrete\Core\Updater\Migrations\Configuration;
use Concrete\Core\Updater\Update;
use Doctrine\DBAL\Migrations\OutputWriter;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:update')
            ->setDescription('Runs all database migrations to bring the concrete5 installation up to date.')
            ->addEnvOption()
            ->setCanRunAsRoot(false)
            ->addOption('rerun', null, InputOption::VALUE_NONE, '(Re)apply already executed migrations')
            ->addOption('after', 'a', InputOption::VALUE_REQUIRED, '(Re)apply migrations after a specific version or migration (requires --rerun)')
            ->addOption('since', 's', InputOption::VALUE_REQUIRED, '(Re)apply migrations starting from a specific version or migration (requires --rerun)')
            ->setHelp(<<<EOT
Examples:

  ./concrete/bin/concrete5 c5:update
    Execute the migrations that haven't yet been executed

  ./concrete/bin/concrete5 c5:update --rerun
    (Re)Execute all the migrations, including the ones that have already been executed (if possible).

  ./concrete/bin/concrete5 c5:update --rerun --after=8.3.1
    Execute the migrations after the 8.3.1 release (even if they have already been marked as executed)

  ./concrete/bin/concrete5 c5:update --rerun --after=20171218000000
    Execute the migrations after the 20171218000000 migration (even if they have already been marked as executed)

  ./concrete/bin/concrete5 c5:update --rerun --since=8.3.1
    Execute the migrations starting from the 8.3.1 release (even if they have already been marked as executed)

  ./concrete/bin/concrete5 c5:update --rerun --since=20171218000000
    Execute the migrations starting from the 20171218000000 migration (even if they have already been marked as executed)

Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $initialMigration = null;
        if ($input->getOption('rerun')) {
            if ($input->getOption('after') === null && $input->getOption('since') === null) {
                $initialMigration = 'max';
            } elseif ($input->getOption('after') === null) {
                $initialMigration = [$input->getOption('since'), Configuration::FORCEDMIGRATION_INCLUSIVE];
            } elseif ($input->getOption('since') === null) {
                $initialMigration = [$input->getOption('after'), Configuration::FORCEDMIGRATION_EXCLUSIVE];
            } else {
                throw new Exception('You can\'t specify both the --after and --since options.');
            }
        } elseif ($input->getOption('after') !== null || $input->getOption('since') !== null) {
            throw new Exception('The --after / --since options require the --rerun option.');
        }
        $configuration = new Configuration();
        $configuration->setOutputWriter(new OutputWriter(function ($message) use ($output) {
            $output->writeln($message);
        }));
        if ($initialMigration !== null) {
            if ($initialMigration === 'max') {
                $configuration->forceMaxInitialMigration();
            } else {
                $configuration->forceInitialMigration($initialMigration[0], $initialMigration[1]);
            }
            if ($configuration->getForcedInitialMigration() !== null) {
                if ($output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
                    $output->writeln(sprintf('Initial migration to be executed: %s', $configuration->getForcedInitialMigration()->getVersion()));
                }
            }
        }
        $app = Application::getFacadeApplication();
        $app->make(MutexInterface::class)->execute(Update::MUTEX_KEY, function () use ($configuration) {
            Update::updateToCurrentVersion($configuration);
        });
    }
}

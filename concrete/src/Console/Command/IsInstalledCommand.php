<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class IsInstalledCommand extends Command
{
    const CONCRETE_IS_INSTALLED = self::SUCCESS;
    const CONCRETE_IS_NOT_INSTALLED = 1;
    const FAILURE = 2;

    protected function configure()
    {
        $installedExitCode = static::CONCRETE_IS_INSTALLED;
        $notInstalledExitCode = static::CONCRETE_IS_NOT_INSTALLED;
        $errExitCode = static::FAILURE;

        $this
            ->setName('c5:is-installed')
            ->setDescription('Check if Concrete is already installed')
            ->addEnvOption()
            ->setHelp(<<<EOT
This command will print out if Concrete CMS is already installed (unless the --quiet option is specified),
and set the following return codes

  $installedExitCode Concrete is installed
  $notInstalledExitCode Concrete is not installed
  $errExitCode errors occurred
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $app = Application::getFacadeApplication();
            $isInstalled = $app->isInstalled();
            if ($output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
                $output->writeln($isInstalled ? 'Concrete is installed' : 'Concrete is not installed');
            }

            return $isInstalled ? static::CONCRETE_IS_INSTALLED : static::CONCRETE_IS_NOT_INSTALLED;
        } catch (Throwable $error) {
            $this->writeError($output, $error);
            return static::FAILURE;
        }
    }
}

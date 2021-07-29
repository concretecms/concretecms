<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IsInstalledCommand extends Command
{
    const RETURN_CODE_ON_FAILURE = 2;

    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;

        $this
            ->setName('c5:is-installed')
            ->setDescription('Check if Concrete is already installed')
            ->addEnvOption()
            ->setHelp(<<<EOT
This command will print out if Concrete CMS is already installed (unless the --quiet option is specified),
and set the following return codes

  0 Concrete is installed
  1 Concrete is not installed
  $errExitCode errors occurred
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();
        $isInstalled = $app->isInstalled();
        if ($output->getVerbosity() >= OutputInterface::OUTPUT_NORMAL) {
            $output->writeln($isInstalled ? 'Concrete is installed' : 'Concrete is not installed');
        }

        return $isInstalled ? 0 : 1;
    }
}

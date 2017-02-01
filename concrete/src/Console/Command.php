<?php
namespace Concrete\Core\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputOption;

abstract class Command extends SymfonyCommand
{
    /**
     * Add the "env" option to the command options.
     *
     * @return static
     */
    protected function addEnvOption()
    {
        $this->addOption('env', null, InputOption::VALUE_REQUIRED, 'The environment (if not specified, we\'ll work with the configuration item valid for all environments)');

        return $this;
    }
}

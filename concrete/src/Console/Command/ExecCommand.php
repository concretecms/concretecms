<?php

namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExecCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:exec')
            ->setDescription('Execute a PHP script within the Concrete environment')
            ->addEnvOption()
            ->addArgument('script', InputArgument::REQUIRED, 'The path of the script to be executed')
            ->addArgument('arguments', InputArgument::IS_ARRAY, 'The arguments to pass to the script')
            ->setHelp(
                <<<'EOT'
In the included script you'll have these variables:
- $input: an instance of \Symfony\Component\Console\Input\InputInterface
- $output: an instance of \Symfony\Component\Console\Input\OutputInterface
- $args: an array of strings containing the arguments specified after the script to be executed 

To specify the command return code, the PHP script can return an integer or define an integer variable named '$rc'.

To pass options to the included script (via the $args variable), you can prepend them with --
For example:
/path/to/concrete/bin/concrete5 c5:exec your.php -- --option1 --option2=value argument1 argument2

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-exec
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!is_file($input->getArgument('script'))) {
            throw new Exception(sprintf('Unable to find the file %s', $input->getArgument('script')));
        }
        $args = $input->getArgument('arguments');
        $result = require $input->getArgument('script');
        if (is_numeric($result)) {
            return (int) $result;
        }

        return is_numeric($rc ?? null) ? (int) $rc : static::SUCCESS;
    }
}

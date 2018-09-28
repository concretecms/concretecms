<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Exception;

class ExecCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:exec')
            ->setDescription('Execute a PHP script within the concrete5 environment')
            ->addEnvOption()
            ->addArgument('script', InputArgument::REQUIRED, 'The path of the script to be executed')
            ->addArgument('arguments', InputArgument::IS_ARRAY, 'The arguments to pass to the script')
            ->setHelp(<<<EOT
In the included script you'll have these variables:
- \$input: an instance of \Symfony\Component\Console\Input\InputInterface
- \$output: an instance of \Symfony\Component\Console\Input\OutputInterface
- \$args: an array of strings containing the arguments specified after the script to be executed 

To specify the command return code, define an int variable named '\$rc'.

Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred

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
        require $input->getArgument('script');

        return isset($rc) ? $rc : 0;
    }
}

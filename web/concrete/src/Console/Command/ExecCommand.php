<?php
namespace Concrete\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Exception;

class ExecCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:exec')
            ->setDescription('Execute a PHP script within the concrete5 environment')
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
  1 errors occurred
EOT
            )
            ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            if (!is_file($input->getArgument('script'))) {
                throw new Exception(sprintf('Unable to find the file %s', $input->getArgument('script')));
            }
            $args = $input->getArgument('arguments');
            require $input->getArgument('script');
            if (!isset($rc)) {
                $rc = 0;
            }
        } catch (Exception $x) {
            $output->writeln('<error>'.$x->getMessage().'</error>');
            $rc = 1;
        }

        return $rc;
    }
}

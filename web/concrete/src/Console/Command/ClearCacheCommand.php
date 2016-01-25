<?php
namespace Concrete\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Core;
use Exception;

class ClearCacheCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:clear-cache')
            ->setDescription('Clear the concrete5 cache')
            ->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  1 errors occurred
EOT
            )
       ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        try {
            $output->write('Clearing the concrete5 cache... ');
            $cms = Core::make('app');
            $cms->clearCaches();
            $output->writeln('<info>done.</info>');
        } catch (Exception $x) {
            $output->writeln('<error>'.$x->getMessage().'</error>');
            $rc = 1;
        }

        return $rc;
    }
}

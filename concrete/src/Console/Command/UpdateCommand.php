<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Updater\Update;
use Doctrine\DBAL\Migrations\OutputWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Database;
use Core;
use Exception;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class UpdateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:update')
            ->setDescription('Runs all database migrations to bring the concrete5 installation up to date.')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force the update')
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
            if (!$input->getOption('force')) {
                if (!$input->isInteractive()) {
                    throw new Exception("You have to specify the --force option in order to run this command");
                }
                $confirmQuestion = new ConfirmationQuestion('Are you sure you want to update this concrete5 installation?');
                if (!$this->getHelper('question')->ask($input, $output, $confirmQuestion)) {
                    throw new Exception("Operation aborted.");
                }
            }
            $configuration = new \Concrete\Core\Updater\Migrations\Configuration();
            $output = new ConsoleOutput();
            $configuration->setOutputWriter(new OutputWriter(function($message) use ($output) {
                $output->writeln($message);
            }));
            Update::updateToCurrentVersion($configuration);
        } catch (Exception $x) {
            $output->writeln('<error>'.$x->getMessage().'</error>');
            $rc = 1;
        }

        return $rc;
    }
}

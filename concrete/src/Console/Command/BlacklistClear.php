<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BlacklistClear extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:blacklist:clear')
            ->setDescription('Clear blacklist-related data')
            ->addEnvOption()
            ->addOption('failed-login-age', 'f', InputOption::VALUE_REQUIRED, 'Clear failed login attempts older that this number of seconds (0 for all)')
            ->addOption('automatic-bans', 'b', InputOption::VALUE_REQUIRED, 'Clear automatic bans ("expired" to only delete expired bans, "all" to delete the current bans too)')
            ->setHelp(<<<EOT
You can use this command to clear the data related to IP address blacklist.

To clear the failed login attempts data, use the --failed-login-age option.
To clear the automatic bans, use the --automatic-bans option.

Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-blacklist-clear
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = Application::getFacadeApplication();
        $ipService = $app->make('ip');
        /* @var \Concrete\Core\Permission\IPService $ipService */
        $someOperation = false;
        $failedLoginAge = $input->getOption('failed-login-age');
        if ($failedLoginAge !== null) {
            $valn = $app->make('helper/validation/numbers');
            /* @var \Concrete\Core\Utility\Service\Validation\Numbers $valn */
            if (!$valn->integer($failedLoginAge, 0)) {
                throw new UserMessageException('Invalid value of the --failed-login-age option: please specify a non-negative integer.');
            }
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                $output->write("Clearing failed login attempts older that $failedLoginAge seconds... ");
            }
            $count = $ipService->deleteFailedLoginAttempts((int) $failedLoginAge);
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                $output->writeln("$count records deleted.");
            }
            $someOperation = true;
        }
        $automaticBans = $input->getOption('automatic-bans');
        if ($automaticBans !== null) {
            switch ($automaticBans) {
                case 'expired':
                    $onlyExpired = true;
                    break;
                case 'all':
                    $onlyExpired = false;
                    break;
                default:
                    throw new UserMessageException('Invalid value of the --automatic-bans option: valid values are "expired" and "all".');
            }
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                if ($onlyExpired) {
                    $output->write('Deleting the expired automatic bans... ');
                } else {
                    $output->write('Deleting all the automatic bans... ');
                }
            }
            $count = $ipService->deleteAutomaticBlacklist((int) $onlyExpired);
            if ($output->getVerbosity() > OutputInterface::VERBOSITY_QUIET) {
                $output->writeln("$count records deleted.");
            }
            $someOperation = true;
        }
        if ($someOperation === false) {
            throw new UserMessageException('Please specify at least one of the options --failed-login-age option or --automatic-bans');
        }
    }
}

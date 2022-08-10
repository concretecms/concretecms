<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\System\Info;

class InfoCommand extends Command
{
    protected function configure()
    {
        $okExitCode = static::SUCCESS;
        $errExitCode = static::FAILURE;
        $this
            ->setName('c5:info')
            ->setDescription('Get detailed information about this installation.')
            ->addEnvOption()
            ->setHelp(<<<EOT
Returns codes:
  $okExitCode operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-info
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $info = Facade::getFacadeApplication()->make(Info::class);
        /* @var Info $info */

        $output->writeln('<info># Hostname</info>');
        $output->writeln($info->getHostname());
        $output->writeln('');
        $output->writeln('<info># Environment</info>');
        $output->writeln($info->getEnvironment());
        $output->writeln('');
        $output->writeln('<info># Version</info>');
        $output->writeln('Installed - ' . ($info->isInstalled() ? 'Yes' : 'No'));
        $output->writeln($info->getCoreVersions());

        if ($info->isInstalled()) {
            $output->writeln('');
            $output->writeln('<info># Database Information</info>');
            $output->writeln('Version - ' . $info->getDBMSVersion());
            $output->writeln('SQL Mode - ' . $info->getDBMSSqlMode());
        }

        $output->writeln('');
        $output->writeln('<info># Paths</info>');
        $output->writeln('Web root - ' . $info->getWebRootDirectory());
        $output->writeln('Core root - ' . $info->getCoreRootDirectory());

        $output->writeln('');
        $output->writeln('<info># Packages</info>');
        $output->writeln($info->getPackages() ?: 'None');

        $output->writeln('');
        $output->writeln('<info># Overrides</info>');
        $output->writeln($info->getOverrides() ?: 'None');

        $output->writeln('');
        $output->writeln('<info># Cache Settings</info>');
        $output->writeln($info->getCache());

        $output->writeln('');
        $output->writeln('<info># Server API</info>');
        $output->writeln($info->getServerAPI());

        $output->writeln('');
        $output->writeln('<info># PHP Version</info>');
        $output->writeln($info->getPhpVersion());

        $output->writeln('');
        $output->writeln('<info># PHP Extensions</info>');
        $output->writeln(($info->getPhpExtensions() === false ? 'Unable to determine' : $info->getPhpExtensions()));

        $output->writeln('');
        $output->writeln('<info># PHP Settings</info>');
        $output->writeln($info->getPhpSettings());

        return static::SUCCESS;
    }
}

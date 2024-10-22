<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Concrete\Core\Console\ConsoleAwareInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Package;
use Exception;

class UninstallPackageCommand extends Command
{
    protected function configure()
    {
        $okExitCode = static::SUCCESS;
        $errExitCode = static::FAILURE;
        $this
            ->setName('c5:package:uninstall')
            ->setAliases([
                'c5:package-uninstall',
                'c5:uninstall-package',
            ])
            ->addEnvOption()
            ->setCanRunAsRoot(false)
            ->addOption('trash', null, InputOption::VALUE_NONE, 'If this option is specified the package directory will be moved to the trash directory')
            ->addArgument('package', InputArgument::REQUIRED, 'The handle of the package to be uninstalled')
            ->setDescription('Uninstall a Concrete package')
            ->setHelp(<<<EOT
Returns codes:
  $okExitCode operation completed successfully
  $errExitCode errors occurred

More info at https://documentation.concretecms.org/9-x/developers/security/cli-jobs#c5-package-uninstall
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pkgHandle = $input->getArgument('package');

        $output->write("Looking for package '$pkgHandle'... ");
        $pkg = null;
        foreach (Package::getInstalledList() as $installed) {
            if ($installed->getPackageHandle() === $pkgHandle) {
                $pkg = $installed;
                break;
            }
        }
        if ($pkg === null) {
            throw new Exception(sprintf("No package with handle '%s' is installed", $pkgHandle));
        }

        // Provide the console objects to objects that are aware of the console
        if ($pkg instanceof ConsoleAwareInterface) {
            $pkg->setConsole($this->getApplication(), $output, $input);
        }

        $output->writeln(sprintf('<info>found (%s).</info>', $pkg->getPackageName()));

        $output->write('Checking preconditions... ');
        $test = $pkg->testForUninstall();
        if (is_object($test)) {
            /*
             * @var Error $test
             */
            throw new Exception(implode("\n", $test->getList()));
        }
        $output->writeln('<info>good.</info>');

        $output->write('Uninstalling package... ');
        $pkg->uninstall();
        $output->writeln('<info>done.</info>');

        if ($input->getOption('trash')) {
            $output->write('Moving package to trash... ');
            $r = $pkg->backup();
            if ($r instanceof ErrorList) {
                throw new Exception(implode("\n", $r->getList()));
            }
            $output->writeln('<info>done.</info>');
        }

        return static::SUCCESS;
    }
}

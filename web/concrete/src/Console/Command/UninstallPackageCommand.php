<?php
namespace Concrete\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
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
        $this
            ->setName('c5:package-uninstall')
            ->addOption('trash', null, InputOption::VALUE_NONE, 'If this option is specified the package directory will be moved to the trash directory')
            ->addArgument('package', InputArgument::REQUIRED, 'The handle of the package to be uninstalled')
            ->setDescription('Uninstall a concrete5 package')
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
            $output->writeln(sprintf('<info>found (%s).</info>', $pkg->getPackageName()));

            $output->write('Checking preconditions... ');
            $test = $pkg->testForUninstall();
            if ($test !== true) {
                throw new Exception(implode("\n", Package::mapError($test)));
            }
            $output->writeln('<info>good.</info>');

            $output->write('Uninstalling package... ');
            $pkg->uninstall();
            $output->writeln('<info>done.</info>');

            if ($input->getOption('trash')) {
                $output->write('Moving package to trash... ');
                $r = $pkg->backup();
                if (is_array($r)) {
                    throw new Exception(implode("\n", Package::mapError($r)));
                }
                $output->writeln('<info>done.</info>');
            }
        } catch (Exception $x) {
            $output->writeln($x->getMessage());
            $rc = 1;
        }

        return $rc;
    }
}

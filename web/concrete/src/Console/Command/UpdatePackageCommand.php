<?php
namespace Concrete\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Package;
use Exception;

class UpdatePackageCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('c5:package-update')
            ->addArgument('package', InputArgument::REQUIRED, 'The handle of the package to be updated')
            ->setDescription('Update a concrete5 package')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $pkgHandle = $input->getArgument('package');

            $output->write("Looking for updatable package '$pkgHandle'... ");
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
            $output->writeln(sprintf('found (%s).', $pkg->getPackageName()));

            $output->write('Checking preconditions... ');
            $upPkg = null;
            foreach (Package::getLocalUpgradeablePackages() as $updatable) {
                if ($updatable->getPackageHandle() === $pkgHandle) {
                    $upPkg = $updatable;
                    break;
                }
            }
            if ($upPkg === null) {
                $output->writeln(sprintf("the package is already up-to-date (v%s)", $pkg->getPackageVersion()));

                return;
            }
            $test = Package::testForInstall($pkgHandle, false);
            if ($test !== true) {
                throw new Exception(implode("\n", Package::mapError($r)));
            }
            $output->writeln('good.');

            $output->write(sprintf('Updating from v%s to v%s...', $upPkg->getPackageCurrentlyInstalledVersion(), $upPkg->getPackageVersion()));
            $pkg->upgradeCoreData();
            $pkg->upgrade();
            $output->writeln('done.');
        } catch (Exception $x) {
            $output->writeln($x->getMessage());

            return 1;
        }
    }
}

<?php
namespace Concrete\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption('all', null, InputOption::VALUE_NONE, 'Update all the installed packages')
            ->addArgument('package', InputArgument::OPTIONAL, 'The handle of the package to be updated')
            ->setDescription('Update a concrete5 package')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        try {
            $pkgHandle = $input->getArgument('package');
            if ($input->getOption('all')) {
                if ($pkgHandle !== null) {
                    throw new Exception('If you use the --all option you can\'t specify a package handle.');
                }
                $updatablePackages = Package::getLocalUpgradeablePackages();
                if (empty($updatablePackaeges)) {
                    $output->writeln("No package needs to be updated.");
                } else {
                    foreach ($updatablePackages as $pkg) {
                        try {
                            $this->updatePackage($pkg->getPackageHandle(), $output);
                        } catch (Exception $x) {
                            $output->writeln($x->getMessage());
                            $rc = 1;
                        }
                    }
                }
            } elseif ($pkgHandle === null) {
                throw new Exception('No package handle specified and the --all option has not been specified.');
            } else {
                $this->updatePackage($pkgHandle, $output);
            }
        } catch (Exception $x) {
            $output->writeln($x->getMessage());
            $rc = 1;
        }

        return $rc;
    }

    protected function updatePackage($pkgHandle, OutputInterface $output)
    {
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
        } else {
            $test = Package::testForInstall($pkgHandle, false);
            if ($test !== true) {
                throw new Exception(implode("\n", Package::mapError($r)));
            }
            $output->writeln('good.');

            $output->write(sprintf('Updating from v%s to v%s...', $upPkg->getPackageCurrentlyInstalledVersion(), $upPkg->getPackageVersion()));
            $pkg->upgradeCoreData();
            $pkg->upgrade();
            $output->writeln('done.');
        }
    }
}

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
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Update all the installed packages')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update even if the package is already at last version')
            ->addArgument('package', InputArgument::OPTIONAL, 'The handle of the package to be updated')
            ->setDescription('Update a concrete5 package')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        try {
            $pkgHandle = $input->getArgument('package');
            $force = $input->getOption('force');
            if ($input->getOption('all')) {
                if ($pkgHandle !== null) {
                    throw new Exception('If you use the --all option you can\'t specify a package handle.');
                }
                if ($force) {
                    foreach (Package::getInstalledHandles() as $pkgHandle) {
                        try {
                            $this->updatePackage($pkgHandle, $output, $force);
                        } catch (Exception $x) {
                            $output->writeln($x->getMessage());
                            $rc = 1;
                        }
                    }
                } else {
                    $updatablePackages = Package::getLocalUpgradeablePackages();
                    if (empty($updatablePackages)) {
                        $output->writeln("No package needs to be updated.");
                    } else {
                        foreach ($updatablePackages as $pkg) {
                            try {
                                $this->updatePackage($pkg->getPackageHandle(), $output, $force);
                            } catch (Exception $x) {
                                $output->writeln($x->getMessage());
                                $rc = 1;
                            }
                        }
                    }
                }
            } elseif ($pkgHandle === null) {
                throw new Exception('No package handle specified and the --all option has not been specified.');
            } else {
                $this->updatePackage($pkgHandle, $output, $force);
            }
        } catch (Exception $x) {
            $output->writeln($x->getMessage());
            $rc = 1;
        }

        return $rc;
    }

    protected function updatePackage($pkgHandle, OutputInterface $output, $force)
    {
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
        $output->writeln(sprintf('found (%s).', $pkg->getPackageName()));

        $output->write('Checking preconditions... ');
        $upPkg = null;
        foreach (Package::getLocalUpgradeablePackages() as $updatable) {
            if ($updatable->getPackageHandle() === $pkgHandle) {
                $upPkg = $updatable;
                break;
            }
        }
        if ($upPkg === null && $force !== true) {
            $output->writeln(sprintf("the package is already up-to-date (v%s)", $pkg->getPackageVersion()));
        } else {
            $test = Package::testForInstall($pkgHandle, false);
            if ($test !== true) {
                throw new Exception(implode("\n", Package::mapError($r)));
            }
            $output->writeln('good.');

            if ($upPkg === null) {
                $output->write(sprintf('Forcing upgrade at v%s...', $pkg->getPackageVersion()));
                $upPkg = Package::getByHandle($pkgHandle);
            } else {
                $output->write(sprintf('Updating from v%s to v%s...', $upPkg->getPackageCurrentlyInstalledVersion(), $upPkg->getPackageVersion()));
            }
            $upPkg->upgradeCoreData();
            $upPkg->upgrade();
            $output->writeln('done.');
        }
    }
}

<?php
namespace Concrete\Core\Console\Command;

use Concrete\Core\Console\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Concrete\Core\Support\Facade\Package;
use Exception;

class UpdatePackageCommand extends Command
{
    protected function configure()
    {
        $errExitCode = static::RETURN_CODE_ON_FAILURE;
        $this
            ->setName('c5:package-update')
            ->addEnvOption()
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Update all the installed packages')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Force update even if the package is already at last version')
            ->addArgument('packages', InputArgument::IS_ARRAY, 'The handle of the package to be updated (multiple values allowed)')
            ->setDescription('Update a concrete5 package')
            ->setHelp(<<<EOT
Returns codes:
  0 operation completed successfully
  $errExitCode errors occurred

More info at http://documentation.concrete5.org/developers/appendix/cli-commands#c5-package-update
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rc = 0;
        $updatableHandles = [];
        $force = $input->getOption('force');
        if ($input->getOption('all')) {
            if (count($input->getArgument('packages')) > 0) {
                throw new Exception('If you use the --all option you can\'t specify package handles.');
            }
            if ($force) {
                foreach (Package::getInstalledHandles() as $pkgHandle) {
                    $updatableHandles[] = $pkgHandle;
                }
                if (empty($updatableHandles)) {
                    $output->writeln("No package has been found.");
                }
            } else {
                foreach (Package::getLocalUpgradeablePackages() as $pkg) {
                    $updatableHandles[] = $pkg->getPackageHandle();
                }
                if (empty($updatableHandles)) {
                    $output->writeln("No package needs to be updated.");
                }
            }
        } else {
            $updatableHandles = $input->getArgument('packages');
            if (empty($updatableHandles)) {
                throw new Exception('No package handle specified and the --all option has not been specified.');
            }
        }
        foreach ($updatableHandles as $updatableHandle) {
            try {
                $this->updatePackage($updatableHandle, $output, $force);
            } catch (Exception $x) {
                $this->writeError($output, $x);
                $rc = 1;
            }
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
        $output->writeln(sprintf('<info>found (%s).</info>', $pkg->getPackageName()));

        $output->write('Checking preconditions... ');
        $upPkg = null;
        foreach (Package::getLocalUpgradeablePackages() as $updatable) {
            if ($updatable->getPackageHandle() === $pkgHandle) {
                $upPkg = $updatable;
                break;
            }
        }
        if ($upPkg === null && $force !== true) {
            $output->writeln(sprintf("<info>the package is already up-to-date (v%s)</info>", $pkg->getPackageVersion()));
        } else {
            $test = $pkg->testForInstall(false);
            if (is_object($test)) {
                /*
                 * @var Error $test
                 */
                throw new Exception(implode("\n", $test->getList()));
            }
            $output->writeln('<info>good.</info>');

            if ($upPkg === null) {
                $output->write(sprintf('Forcing upgrade at v%s... ', $pkg->getPackageVersion()));
                $upPkg = Package::getByHandle($pkgHandle);
            } else {
                $output->write(sprintf('Updating from v%s to v%s... ', $upPkg->getPackageEntity()->getPackageVersion(), $upPkg->getPackageVersion()));
            }
            $upPkg->upgradeCoreData();
            $upPkg->upgrade();
            $output->writeln('<info>done.</info>');
        }
    }
}

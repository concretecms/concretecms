<?php
namespace Concrete\Core\Package\Dependency;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;

class DependencyChecker
{
    /**
     * @var Application
     */
    protected $application;

    /**
     * @var Package[]|null
     */
    protected $installedPackages;

    /**
     * Initializes the instance.
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Set the list of installed packages.
     *
     * @param Package[] $installedPackages
     *
     * @return $this
     */
    public function setInstalledPackages(array $installedPackages)
    {
        $dictionary = [];
        foreach ($installedPackages as $installedPackage) {
            $dictionary[$installedPackage->getPackageHandle()] = $installedPackage;
        }
        $this->installedPackages = $dictionary;

        return $this;
    }

    /**
     * @param Package $package
     *
     * @return ErrorList
     */
    public function testForInstall(Package $package)
    {
        $result = $this->application->make(ErrorList::class);
        $installedPackages = $this->getInstalledPackages();
        // Check that this package is compatible with the installed packages
        foreach ($installedPackages as $installedPackage) {
            $requirements = $this->getPackageRequirementsForPackage($installedPackage, $package);
            $error = $this->checkPackageCompatibility($installedPackage, $package, $requirements);
            if ($error !== null) {
                $result->add($error);
            }
        }
        // Check that the installed packages are compatible with this package
        foreach ($package->getPackageDependencies() as $handle => $requirements) {
            if (isset($installedPackages[$handle])) {
                $installedPackage = $installedPackages[$handle];
                $error = $this->checkPackageCompatibility($package, $installedPackage, $requirements);
                if ($error !== null) {
                    $result->add($error);
                }
            } else {
                if ($requirements !== false) {
                    $result->add(new MissingRequiredPackageException($package, $handle, $requirements));
                }
            }
        }

        return $result;
    }

    /**
     * @param Package $package
     *
     * @return ErrorList
     */
    public function testForUninstall(Package $package)
    {
        $result = $this->application->make(ErrorList::class);
        foreach ($this->getInstalledPackages() as $installedPackage) {
            $requirements = $this->getPackageRequirementsForPackage($installedPackage, $package);
            if ($requirements !== null && $requirements !== false) {
                $result->add(new RequiredPackageException($package, $installedPackage));
            }
        }

        return $result;
    }

    /**
     * Get the list of installed packages.
     *
     * @return Package[] keys are package handles, values are Package instances
     */
    protected function getInstalledPackages()
    {
        if ($this->installedPackages === null) {
            $installedPackages = [];
            $packageService = $this->application->make(PackageService::class);
            foreach ($packageService->getInstalledHandles() as $packageHandle) {
                $installedPackages[$packageHandle] = $packageService->getClass($packageHandle);
            }
            $this->installedPackages = $installedPackages;
        }

        return $this->installedPackages;
    }

    /**
     * Get the requirements for a package in respect to another package.
     *
     * @param Package $package
     * @param Package $otherPackage
     *
     * @return string[]|string|bool|null
     */
    protected function getPackageRequirementsForPackage(Package $package, Package $otherPackage)
    {
        $dependencies = $package->getPackageDependencies();
        $otherPackageHandle = $otherPackage->getPackageHandle();

        return isset($dependencies[$otherPackageHandle]) ? $dependencies[$otherPackageHandle] : null;
    }

    /**
     * @param Package $package
     * @param Package $dependentPackage
     * @param string[]|string|bool|null $requirements
     *
     * @return \LogicException|null
     */
    protected function checkPackageCompatibility(Package $package, Package $dependentPackage, $requirements)
    {
        $result = null;
        $version = $dependentPackage->getPackageVersion();
        if ($requirements === false) {
            $result = new IncompatiblePackagesException($package, $dependentPackage);
        } elseif (is_string($requirements)) {
            if (version_compare($version, $requirements) < 0) {
                $result = new VersionMismatchException($package, $dependentPackage, $requirements);
            }
        } elseif (is_array($requirements)) {
            if (version_compare($version, $requirements[0]) < 0 || version_compare($version, $requirements[1]) > 0) {
                $result = new VersionMismatchException($package, $dependentPackage, $requirements);
            }
        }

        return $result;
    }
}

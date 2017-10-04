<?php
namespace Concrete\Core\Package\Dependency;

use Concrete\Core\Package\Package;

/**
 * Package dependency failure: a package can't be installed since it requires another package that's not installed.
 */
class MissingRequiredPackageException extends DependencyException
{
    /**
     * The package that can't be installed.
     *
     * @var Package
     */
    protected $notInstallablePackage;

    /**
     * The handle of the package that's not installed.
     *
     * @var Package
     */
    protected $missingPackageHandle;

    /**
     * The version requirements of the not installed package.
     *
     * @var string|string[]|bool
     */
    protected $requirements;

    /**
     * Initialize the instance.
     *
     * @param Package $notInstallablePackage the package that can't be installed
     * @param string $missingPackageHandle the handle of the package that's not installed
     * @param string|string[]|bool $requirements the version requirements of the package
     */
    public function __construct(Package $notInstallablePackage, $missingPackageHandle, $requirements)
    {
        $this->notInstallablePackage = $notInstallablePackage;
        $this->missingPackageHandle = $missingPackageHandle;
        $this->requirements = $requirements;
        if (is_string($requirements)) {
            $message = t(
                'The package "%1$s" requires the package with handle "%2$s" (version %3$s or greater)',
                $notInstallablePackage->getPackageName(),
                $missingPackageHandle,
                $requirements
            );
        } elseif (is_array($requirements)) {
            $message = t(
                'The package "%1$s" requires the package with handle "%2$s" (version between %3$s and %4$s)',
                $notInstallablePackage->getPackageName(),
                $missingPackageHandle,
                $requirements[0],
                $requirements[1]
            );
        } else {
            $message = t(
                'The package "%1$s" requires the package with handle "%2$s"',
                $notInstallablePackage->getPackageName(),
                $missingPackageHandle
            );
        }
        parent::__construct($message);
    }

    /**
     * Get the package that can't be installed.
     *
     * @return Package
     */
    public function getNotInstallablePackage()
    {
        return $this->notInstallablePackage;
    }

    /**
     * Get the handle of the package that's not installed.
     *
     * @return Package
     */
    public function getMissingPackageHandle()
    {
        return $this->missingPackageHandle;
    }

    /**
     * Get the version requirements of the not installed package.
     *
     * @var string|string[]|bool
     */
    public function getRequirements()
    {
        return $this->requirements;
    }
}

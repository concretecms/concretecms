<?php
namespace Concrete\Core\Package\Dependency;

use Concrete\Core\Package\Package;

/**
 * Package dependency failure: an installed package can't be uninstalled since it's required by another package.
 */
class RequiredPackageException extends DependencyException
{
    /**
     * The package that can't be uninstalled.
     *
     * @var Package
     */
    protected $uninstallablePackage;

    /**
     * The package that requires the package that can't be uninstalled.
     *
     * @var Package
     */
    protected $blockingPackage;

    /**
     * Initialize the instance.
     *
     * @param Package $uninstallablePackage the package that can't be uninstalled
     * @param Package $blockingPackage the package that requires the package that can't be uninstalled
     */
    public function __construct(Package $uninstallablePackage, Package $blockingPackage)
    {
        $this->uninstallablePackage = $uninstallablePackage;
        $this->blockingPackage = $blockingPackage;
        parent::__construct(t(
            'The package "%1$s" can\'t be uninstalled since the package "%2$s" requires it.',
            $uninstallablePackage->getPackageName(),
            $blockingPackage->getPackageName()
        ));
    }

    /**
     * Get the package that can't be uninstalled.
     *
     * @return Package
     */
    public function getUninstallablePackage()
    {
        return $this->uninstallablePackage;
    }

    /**
     * Get the package that requires the package that can't be uninstalled.
     *
     * @return Package
     */
    public function getBlockingPackage()
    {
        return $this->blockingPackage;
    }
}

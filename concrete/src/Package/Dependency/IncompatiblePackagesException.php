<?php
namespace Concrete\Core\Package\Dependency;

use Concrete\Core\Package\Package;

/**
 * Package dependency failure: a package doesn't want another package.
 */
class IncompatiblePackagesException extends DependencyException
{
    /**
     * The package that doesn't want the other package.
     *
     * @var Package
     */
    protected $blockingPackage;

    /**
     * The incompatible package.
     *
     * @var Package
     */
    protected $incompatiblePackage;

    /**
     * Initialize the instance.
     *
     * @param Package $blockingPackage the package that doesn't want the other package
     * @param Package $incompatiblePackage the incompatible package
     */
    public function __construct(Package $blockingPackage, Package $incompatiblePackage)
    {
        $this->blockingPackage = $blockingPackage;
        $this->incompatiblePackage = $incompatiblePackage;
        parent::__construct(t(
            'The package "%1$s" can\'t be installed if the package "%2$s" is installed.',
            $incompatiblePackage->getPackageName(),
            $blockingPackage->getPackageName()
        ));
    }

    /**
     * Get the package that can't be uninstalled.
     *
     * @return Package
     */
    public function getBlockingPackage()
    {
        return $this->blockingPackage;
    }

    /**
     * Get the incompatible package.
     *
     * @return Package
     */
    public function getIncompatiblePackage()
    {
        return $this->incompatiblePackage;
    }
}

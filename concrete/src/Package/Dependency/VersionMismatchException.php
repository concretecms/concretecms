<?php

namespace Concrete\Core\Package\Dependency;

use Concrete\Core\Package\Package;

/**
 * Package dependency failure: a package requires specific versions of another package.
 */
class VersionMismatchException extends DependencyException
{
    /**
     * The package that causes the dependency problem.
     *
     * @var \Concrete\Core\Package\Package
     */
    protected $blockingPackage;

    /**
     * The package that fails the requirement.
     *
     * @var \Concrete\Core\Package\Package
     */
    protected $package;

    /**
     * The required package version.
     *
     * @var string|string[]
     */
    protected $requiredVersion;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\Package\Package $blockingPackage the package that causes the dependency problem
     * @param \Concrete\Core\Package\Package $package the package that fails the requirement
     * @param string|string[] $requiredVersion the required package version
     */
    public function __construct(Package $blockingPackage, Package $package, $requiredVersion)
    {
        $this->package = $package;
        $this->blockingPackage = $blockingPackage;
        $this->requiredVersion = $requiredVersion;
        if (is_array($requiredVersion)) {
            $message = t(
                'The package "%1$s" requires that package "%2$s" has a version between %3$s and %4$s',
                $blockingPackage->getPackageName(),
                $package->getPackageName(),
                $requiredVersion[0],
                $requiredVersion[1]
            );
        } else {
            $message = t(
                'The package "%1$s" requires that package "%2$s" has a version %3$s or greater',
                $blockingPackage->getPackageName(),
                $package->getPackageName(),
                $requiredVersion
            );
        }
        parent::__construct($message);
    }

    /**
     * Get the package that causes the dependency problem.
     *
     * @return \Concrete\Core\Package\Package
     */
    public function getBlockingPackage()
    {
        return $this->blockingPackage;
    }

    /**
     * Get the package that fails the requirement.
     *
     * @return \Concrete\Core\Package\Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * Get the required package version.
     *
     * @return string|string[]
     */
    public function getRequiredVersion()
    {
        return $this->requiredVersion;
    }
}

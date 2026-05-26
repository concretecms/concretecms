<?php

declare(strict_types=1);

namespace Concrete\Core\Package\Event;

use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Informational event fired before (on_before_package_uninstall) and after (on_after_package_uninstall) a package is uninstalled.
 * When on_before_package_uninstall fires, the package entity is still intact.
 * When on_after_package_uninstall fires, the package row has been deleted — getPackage()->getPackageEntity() returns a detached entity.
 * To prevent uninstall with an error message, use UninstallEvent with on_package_test_for_uninstall instead.
 */
class PackageEvent
{
    /**
     * @var Package
     */
    protected $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    public function getPackage(): Package
    {
        return $this->package;
    }
}

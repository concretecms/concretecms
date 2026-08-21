<?php

declare(strict_types=1);

namespace Concrete\Core\Package\Event;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Event fired by testForUpgrade() as on_package_test_for_upgrade.
 * Listeners add errors to getError() to block the upgrade with a user-visible message.
 *
 * Note: there is an optional bootstrap auto-upgrade path in Application::setupPackages().
 * It is disabled by default and has no dashboard UI; it can only be enabled by setting
 * concrete.updates.enable_auto_update_packages in application/config/concrete.php.
 * When active, it calls upgrade() directly and bypasses testForUpgrade(), so this event
 * is never dispatched on that path.
 *
 * If your package requires a hard precondition on all upgrade paths, consider overriding
 * upgrade() in your Package subclass to call testForUpgrade() first, or hooking into
 * on_before_package_upgrade to perform a final check before the schema migration runs.
 */
class UpgradeEvent extends PackageEvent
{
    /**
     * @var ErrorList
     */
    protected $error;

    public function __construct(Package $package, ErrorList $error)
    {
        parent::__construct($package);
        $this->error = $error;
    }

    /**
     * Add errors to this list to prevent the package from being upgraded.
     */
    public function getError(): ErrorList
    {
        return $this->error;
    }
}

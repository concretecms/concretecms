<?php

declare(strict_types=1);

namespace Concrete\Core\Package\Event;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Event fired by testForUninstall() as on_package_test_for_uninstall.
 * Listeners add errors to getError() to block the uninstall with a user-visible message.
 */
class UninstallEvent extends PackageEvent
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
     * Add errors to this list to prevent the package from being uninstalled.
     */
    public function getError(): ErrorList
    {
        return $this->error;
    }
}

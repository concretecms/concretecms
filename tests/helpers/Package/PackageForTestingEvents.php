<?php

declare(strict_types=1);

namespace Concrete\TestHelpers\Package;

use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class PackageForTestingEvents extends Package
{
    public function getPackagePath(): string
    {
        return __DIR__;
    }

    public function getPackageHandle(): string
    {
        return 'test_package';
    }

    public function getEventDispatcherForTest(): EventDispatcher
    {
        return $this->getEventDispatcher();
    }

    // Return null so uninstall() skips proxy-class generation, which requires a live entity manager.
    public function getPackageEntityManager(): ?\Doctrine\ORM\EntityManager
    {
        return null;
    }
}

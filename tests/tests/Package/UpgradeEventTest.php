<?php

declare(strict_types=1);

namespace Concrete\Tests\Package;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Package\Event\UpgradeEvent;
use Concrete\Core\Package\Package;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the UpgradeEvent class in isolation.
 *
 * Integration coverage: the dispatch of on_package_test_for_upgrade from Package::testForUpgrade()
 * is covered in PackageUpgradeEventDispatchTest, which also tests the veto and error-propagation
 * paths. Unlike the uninstall equivalent, testForUpgrade() has no live-database dependency, so
 * full unit isolation is achievable.
 */
class UpgradeEventTest extends TestCase
{
    public function testGetPackage(): void
    {
        $package = \Mockery::mock(Package::class);
        $error = new ErrorList();

        $event = new UpgradeEvent($package, $error);

        static::assertSame($package, $event->getPackage());
    }

    public function testGetErrorReturnsSameInstance(): void
    {
        $package = \Mockery::mock(Package::class);
        $error = new ErrorList();

        $event = new UpgradeEvent($package, $error);

        static::assertSame($error, $event->getError());
    }

    public function testErrorsAddedByListenerAreReflectedInOriginalList(): void
    {
        $package = \Mockery::mock(Package::class);
        $error = new ErrorList();

        $event = new UpgradeEvent($package, $error);

        // Simulate what a listener would do to veto the upgrade
        $event->getError()->add('Package B requires Package A >= 2.0; upgrading would break it');

        static::assertTrue($error->has());
        static::assertCount(1, $error->getList());
    }
}

<?php

declare(strict_types=1);

namespace Concrete\Tests\Package;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Package\Event\UninstallEvent;
use Concrete\Core\Package\Package;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests the UninstallEvent class in isolation.
 *
 * Integration coverage note: the dispatch of on_package_test_for_uninstall from
 * Package::testForUninstall() is not covered here because that method calls
 * Theme::getSiteTheme() unconditionally, which requires a live database. A future
 * refactor guarding that call behind a non-empty themes check would make it testable,
 * but that change is out of scope here. Verify end-to-end behavior manually via the
 * dashboard or the c5:package:uninstall CLI command.
 */
class UninstallEventTest extends TestCase
{
    public function testGetPackage(): void
    {
        $package = \Mockery::mock(Package::class);
        $error = new ErrorList();

        $event = new UninstallEvent($package, $error);

        static::assertSame($package, $event->getPackage());
    }

    public function testGetErrorReturnsSameInstance(): void
    {
        $package = \Mockery::mock(Package::class);
        $error = new ErrorList();

        $event = new UninstallEvent($package, $error);

        static::assertSame($error, $event->getError());
    }

    public function testErrorsAddedByListenerAreReflectedInOriginalList(): void
    {
        $package = \Mockery::mock(Package::class);
        $error = new ErrorList();

        $event = new UninstallEvent($package, $error);

        // Simulate what a listener would do to veto the uninstall
        $event->getError()->add('Package B is required by Package A');

        static::assertTrue($error->has());
        static::assertCount(1, $error->getList());
    }
}

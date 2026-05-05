<?php

declare(strict_types=1);

namespace Concrete\Tests\Foundation;

use Concrete\Core\Foundation\Composer;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class ComposerTest extends TestCase
{
    public function testCoreInstalledViaComposer(): void
    {
        $service = app(Composer::class);
        static::assertFalse($service->isCoreInstalledViaComposer());
    }

    public function testNoPackagesInstalledViaComposer(): void
    {
        $service = app(Composer::class);
        static::assertSame([], $service->getPackagesInstalledViaComposer());
    }

    public function testPackageNotInstalledViaComposer(): void
    {
        $service = app(Composer::class);
        static::assertFalse($service->isPackageInstalledViaComposer('not_installed_package_handle'));
    }
}

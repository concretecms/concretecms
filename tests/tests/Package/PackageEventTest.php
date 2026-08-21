<?php

declare(strict_types=1);

namespace Concrete\Tests\Package;

use Concrete\Core\Package\Event\PackageEvent;
use Concrete\Core\Package\Package;
use Concrete\Tests\TestCase;

defined('C5_EXECUTE') or die('Access Denied.');

class PackageEventTest extends TestCase
{
    public function testGetPackage(): void
    {
        $package = \Mockery::mock(Package::class);
        $event = new PackageEvent($package);

        static::assertSame($package, $event->getPackage());
    }
}

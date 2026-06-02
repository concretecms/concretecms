<?php

declare(strict_types=1);

namespace Concrete\Tests\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Package\Event\PackageEvent;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\TestHelpers\Package\PackageForTestingEvents;
use Concrete\Tests\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests that Package::uninstall() dispatches on_before_package_uninstall and on_after_package_uninstall.
 *
 * Package::testForUninstall() dispatches on_package_test_for_uninstall, but that method calls
 * Theme::getSiteTheme() unconditionally, blocking unit isolation. Error-propagation through
 * UninstallEvent is already covered in UninstallEventTest.
 */
class PackageUninstallEventDispatchTest extends TestCase
{
    private function createMockApp(EventDispatcher $dispatcher): Application
    {
        $manager = M::mock(Manager::class);
        $manager->shouldReceive('getPackageItemCategories')->andReturn([]);

        $config = M::mock();
        $config->shouldReceive('clearNamespace');

        $em = M::mock(EntityManagerInterface::class);
        $em->shouldReceive('remove');
        $em->shouldReceive('flush');

        $navCache = M::mock(NavigationCache::class);
        $navCache->shouldReceive('clear');

        $app = M::mock(Application::class);
        $app->shouldReceive('make')->with(EventDispatcher::class)->andReturn($dispatcher);
        $app->shouldReceive('make')->with(Manager::class, M::type('array'))->andReturn($manager);
        $app->shouldReceive('make')->with('config')->andReturn($config);
        $app->shouldReceive('make')->with('config/database')->andReturn($config);
        $app->shouldReceive('make')->with(EntityManagerInterface::class)->andReturn($em);
        $app->shouldReceive('make')->with(NavigationCache::class)->andReturn($navCache);

        return $app;
    }

    private function createPackage(Application $app): PackageForTestingEvents
    {
        $package = new PackageForTestingEvents($app);
        $package->setPackageEntity(M::mock(PackageEntity::class));

        return $package;
    }

    public function testGetEventDispatcherReturnsInstanceFromContainer(): void
    {
        $dispatcher = M::mock(EventDispatcher::class);

        $app = M::mock(Application::class);
        $app->shouldReceive('make')->with(EventDispatcher::class)->once()->andReturn($dispatcher);

        $method = new \ReflectionMethod(PackageForTestingEvents::class, 'getEventDispatcher');
        $method->setAccessible(true);
        $result = $method->invoke(new PackageForTestingEvents($app));

        static::assertSame($dispatcher, $result);
    }

    public function testUninstallDispatchesOnPackageUninstallThenOnPackageUninstalled(): void
    {
        $callLog = [];

        $dispatcher = M::mock(EventDispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->twice()
            ->andReturnUsing(static function (string $name) use (&$callLog): void {
                $callLog[] = $name;
            })
        ;

        $this->createPackage($this->createMockApp($dispatcher))->uninstall();

        static::assertSame(['on_before_package_uninstall', 'on_after_package_uninstall'], $callLog);
    }

    public function testUninstallEventsExposeThePackageInstance(): void
    {
        $captured = [];

        $dispatcher = M::mock(EventDispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->twice()
            ->andReturnUsing(static function (string $name, PackageEvent $event) use (&$captured): void {
                $captured[$name] = $event->getPackage();
            })
        ;

        $app = $this->createMockApp($dispatcher);
        $package = $this->createPackage($app);
        $package->uninstall();

        static::assertSame($package, $captured['on_before_package_uninstall']);
        static::assertSame($package, $captured['on_after_package_uninstall']);
    }
}

<?php

declare(strict_types=1);

namespace Concrete\Tests\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FavoritesNavigationCache;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Entity\Package as PackageEntity;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Package\Dependency\DependencyChecker;
use Concrete\Core\Package\Event\PackageEvent;
use Concrete\Core\Package\Event\UpgradeEvent;
use Concrete\Core\Package\ItemCategory\Manager;
use Concrete\TestHelpers\Package\PackageForTestingEvents;
use Concrete\Tests\TestCase;
use Mockery as M;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Tests that Package::upgrade() dispatches on_before_package_upgrade and on_after_package_upgrade,
 * and that Package::testForUpgrade() dispatches on_package_test_for_upgrade.
 *
 * Unlike testForUninstall(), testForUpgrade() does not call Theme::getSiteTheme(), so the
 * veto event dispatch and error-propagation paths can be fully unit-tested here.
 */
class PackageUpgradeEventDispatchTest extends TestCase
{
    private function createMockApp(EventDispatcher $dispatcher): Application
    {
        $blockTypeDriver = M::mock();
        $blockTypeDriver->shouldReceive('getItems')->andReturn([]);

        $manager = M::mock(Manager::class);
        $manager->shouldReceive('driver')->with('block_type')->andReturn($blockTypeDriver);

        $dependencyChecker = M::mock(DependencyChecker::class);
        $dependencyChecker->shouldReceive('testForInstall')->andReturn(new ErrorList());

        $navCache = M::mock(NavigationCache::class);
        $navCache->shouldReceive('clear');

        $favNavCache = M::mock(FavoritesNavigationCache::class);
        $favNavCache->shouldReceive('clear');

        $app = M::mock(Application::class);
        $app->shouldReceive('make')->with(EventDispatcher::class)->andReturn($dispatcher);
        $app->shouldReceive('make')->with(Manager::class, M::type('array'))->andReturn($manager);
        $app->shouldReceive('make')->with('error')->andReturnUsing(static function (): ErrorList {
            return new ErrorList();
        });
        $app->shouldReceive('build')->with(DependencyChecker::class)->andReturn($dependencyChecker);
        $app->shouldReceive('make')->with(NavigationCache::class)->andReturn($navCache);
        $app->shouldReceive('make')->with(FavoritesNavigationCache::class)->andReturn($favNavCache);

        return $app;
    }

    private function createPackage(Application $app): PackageForTestingEvents
    {
        $package = new class ($app) extends PackageForTestingEvents {
            // Stub out the DB schema import so upgrade() can run without a live database.
            public function upgradeDatabase(): void
            {
            }
        };
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

    public function testUpgradeDispatchesBeforeAndAfterInOrder(): void
    {
        $callLog = [];

        $dispatcher = M::mock(EventDispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->twice()
            ->andReturnUsing(static function (string $name) use (&$callLog): void {
                $callLog[] = $name;
            })
        ;

        $this->createPackage($this->createMockApp($dispatcher))->upgrade();

        static::assertSame(['on_before_package_upgrade', 'on_after_package_upgrade'], $callLog);
    }

    public function testUpgradeEventsExposeThePackageInstance(): void
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
        $package->upgrade();

        static::assertSame($package, $captured['on_before_package_upgrade']);
        static::assertSame($package, $captured['on_after_package_upgrade']);
    }

    public function testTestForUpgradeDispatchesVetoEvent(): void
    {
        $dispatched = [];

        $dispatcher = M::mock(EventDispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->andReturnUsing(static function (string $name) use (&$dispatched): void {
                $dispatched[] = $name;
            })
        ;

        $app = $this->createMockApp($dispatcher);

        // Override testForInstall() so it passes without needing APP_VERSION or a live DependencyChecker
        $package = new class ($app) extends PackageForTestingEvents {
            public function testForInstall($testForAlreadyInstalled = true)
            {
                return true;
            }
        };
        $package->setPackageEntity(M::mock(PackageEntity::class));

        $package->testForUpgrade();

        static::assertSame(['on_package_test_for_upgrade'], $dispatched);
    }

    public function testListenerVetoBlocksUpgrade(): void
    {
        $dispatcher = M::mock(EventDispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->andReturnUsing(static function (string $name, UpgradeEvent $event): void {
                $event->getError()->add('Package B requires Package A >= 2.0');
            })
        ;

        $app = $this->createMockApp($dispatcher);

        $package = new class ($app) extends PackageForTestingEvents {
            public function testForInstall($testForAlreadyInstalled = true)
            {
                return true;
            }
        };
        $package->setPackageEntity(M::mock(PackageEntity::class));

        $result = $package->testForUpgrade();

        static::assertInstanceOf(ErrorList::class, $result);
        static::assertTrue($result->has());
    }

    public function testTestForInstallFailurePropagatesIntoVetoEvent(): void
    {
        $capturedEvent = null;

        $dispatcher = M::mock(EventDispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->andReturnUsing(static function (string $name, UpgradeEvent $event) use (&$capturedEvent): void {
                $capturedEvent = $event;
            })
        ;

        $app = $this->createMockApp($dispatcher);

        $installErrors = new ErrorList();
        $installErrors->add('Requires ConcreteCMS >= 99.0');

        $package = new class ($app, $installErrors) extends PackageForTestingEvents {
            /**
             * @var ErrorList
             */
            private $installErrors;

            public function __construct(Application $app, ErrorList $installErrors)
            {
                parent::__construct($app);
                $this->installErrors = $installErrors;
            }

            public function testForInstall($testForAlreadyInstalled = true)
            {
                return $this->installErrors;
            }
        };
        $package->setPackageEntity(M::mock(PackageEntity::class));

        $result = $package->testForUpgrade();

        static::assertInstanceOf(ErrorList::class, $result);
        static::assertInstanceOf(UpgradeEvent::class, $capturedEvent);
        static::assertTrue($capturedEvent->getError()->has());
    }

    public function testErrorListIsFreshOnEachCall(): void
    {
        $callCount = 0;
        $dispatcher = M::mock(EventDispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->twice()
            ->andReturnUsing(static function (string $name, UpgradeEvent $event) use (&$callCount): void {
                $callCount++;
                if ($callCount === 1) {
                    $event->getError()->add('blocking error');
                }
                // Second call: no errors added — result must be true.
            })
        ;

        $app = $this->createMockApp($dispatcher);

        $package = new class ($app) extends PackageForTestingEvents {
            public function testForInstall($testForAlreadyInstalled = true)
            {
                return true;
            }
        };
        $package->setPackageEntity(M::mock(PackageEntity::class));

        $first = $package->testForUpgrade();
        static::assertInstanceOf(ErrorList::class, $first);

        // Second call must not carry over errors from the first — each call gets a fresh ErrorList.
        $second = $package->testForUpgrade();
        static::assertTrue($second);
    }

    public function testInstallErrorsAndListenerErrorsAreFlattenedInResult(): void
    {
        $dispatcher = M::mock(EventDispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->andReturnUsing(static function (string $name, UpgradeEvent $event): void {
                $event->getError()->add('listener error');
            })
        ;

        $app = $this->createMockApp($dispatcher);

        $installErrors = new ErrorList();
        $installErrors->add('install error 1');
        $installErrors->add('install error 2');

        $package = new class ($app, $installErrors) extends PackageForTestingEvents {
            /**
             * @var ErrorList
             */
            private $installErrors;

            public function __construct(Application $app, ErrorList $installErrors)
            {
                parent::__construct($app);
                $this->installErrors = $installErrors;
            }

            public function testForInstall($testForAlreadyInstalled = true)
            {
                return $this->installErrors;
            }
        };
        $package->setPackageEntity(M::mock(PackageEntity::class));

        $result = $package->testForUpgrade();

        static::assertInstanceOf(ErrorList::class, $result);
        // 2 from testForInstall() + 1 from the listener — must be flat, not nested.
        static::assertCount(3, $result->getList());
    }
}

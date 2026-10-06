<?php

namespace Concrete\Tests\Package;

use Concrete\Core\Application\Application;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Package\Dependency\DependencyChecker;
use Concrete\Core\Package\Package;
use Concrete\TestHelpers\Package\PackageForTestingPHPVersion;
use Concrete\Tests\TestCase;
use Mockery as M;
use PHPUnit\Framework\Attributes\DataProvider;

class MinimumPHPVersionTest extends TestCase
{
    public static function installProvider(): array
    {
        return [
            [
                self::createPackage(''),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION])),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION - 1])),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION + 1])),
                false,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION])),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION - 1])),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION + 1])),
                false,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION - 1, PHP_MINOR_VERSION])),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION + 1, PHP_MINOR_VERSION])),
                false,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION])),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION - 1])),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION, PHP_RELEASE_VERSION + 1])),
                false,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION - 1, PHP_RELEASE_VERSION])),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION, PHP_MINOR_VERSION + 1, PHP_RELEASE_VERSION])),
                false,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION - 1, PHP_MINOR_VERSION, PHP_RELEASE_VERSION])),
                true,
            ],
            [
                self::createPackage(implode('.', [PHP_MAJOR_VERSION + 1, PHP_MINOR_VERSION, PHP_RELEASE_VERSION])),
                false,
            ],
            [
                self::createPackage('99.1.2'),
                false,
            ],
        ];
    }

    #[DataProvider('installProvider')]
    public function testTestForInstall(Package $package, bool $shouldInstall)
    {
        $actual = $package->testForInstall(false);
        if ($shouldInstall) {
            $this->assertSame(true, $actual);
        } else {
            $this->assertInstanceOf(ErrorList::class, $actual);
            $this->assertNotSame('', (string) $actual);
        }
    }

    private static function createPackage(string $minPHPVersion): Package
    {
        $dependencyChecker = M::mock(Application::class);
        /** @var \Mockery\MockInterface $dependencyChecker */
        $dependencyChecker->shouldReceive('testForInstall')->andReturn(app('error'));
        $app = M::mock(Application::class);
        /** @var \Mockery\MockInterface $app */
        $app->shouldReceive('make')->with('error')->andReturn(app('error'));
        $app->shouldReceive('make')->zeroOrMoreTimes()->with('config')->andReturn(app('config'));
        $app->shouldReceive('build')->with(DependencyChecker::class)->andReturn($dependencyChecker);
        $package = new PackageForTestingPHPVersion($app);
        $package->setPHPVersionRequired($minPHPVersion);
        
        return $package;
    }
}

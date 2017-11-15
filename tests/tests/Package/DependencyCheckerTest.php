<?php

namespace Concrete\Tests\Package;

use Concrete\Core\Package\Dependency\DependencyChecker;
use Concrete\Core\Package\Dependency\IncompatiblePackagesException;
use Concrete\Core\Package\Dependency\MissingRequiredPackageException;
use Concrete\Core\Package\Dependency\RequiredPackageException;
use Concrete\Core\Package\Dependency\VersionMismatchException;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Application;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use ReflectionException;

class DependencyCheckerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    private static $app;

    /**
     * @var DependencyChecker
     */
    private static $checker;

    public static function setupBeforeClass()
    {
        self::$app = Application::getFacadeApplication();
        self::$checker = self::$app->build(DependencyChecker::class);
    }

    public function testForInstallProvider()
    {
        $packages = [
            self::createPackage('handle0', '0.1', 'Name 0', []),
            self::createPackage('handle1', '1.1', 'Name 1', ['handle0' => true]),
            self::createPackage('handle2', '2.1', 'Name 2', ['handle1' => false]),
            self::createPackage('handle3', '3.1', 'Name 3', ['handle0' => '0.1']),
            self::createPackage('handle4', '4.1', 'Name 4', ['handle0' => '99']),
            self::createPackage('handle5', '5.1', 'Name 5', ['handle0' => ['0.1', '0.1.99']]),
            self::createPackage('handle6', '6.1', 'Name 6', ['handle0' => ['99.0', '99.1']]),
        ];

        return [
            [
                $packages[0],
                [],
            ],
            [
                $packages[1],
                [],
                [
                    new MissingRequiredPackageException($packages[1], 'handle0', true),
                ],
            ],
            [
                $packages[1],
                [
                    $packages[0],
                ],
            ],
            [
                $packages[2],
                [
                    $packages[0],
                ],
            ],
            [
                $packages[2],
                [
                    $packages[1],
                ],
                [
                    new IncompatiblePackagesException($packages[2], $packages[1]),
                ],
            ],
            [
                $packages[3],
                [
                ],
                [
                    new MissingRequiredPackageException($packages[3], 'handle0', '0.1'),
                ],
            ],
            [
                $packages[3],
                [
                    $packages[0],
                ],
            ],
            [
                $packages[4],
                [
                ],
                [
                    new MissingRequiredPackageException($packages[4], 'handle0', '99'),
                ],
            ],
            [
                $packages[4],
                [
                    $packages[0],
                ],
                [
                    new VersionMismatchException($packages[4], $packages[0], '99'),
                ],
            ],

            [
                $packages[5],
                [
                ],
                [
                    new MissingRequiredPackageException($packages[5], 'handle0', ['0.1', '0.1.99']),
                ],
            ],
            [
                $packages[5],
                [
                    $packages[0],
                ],
            ],
            [
                $packages[6],
                [
                ],
                [
                    new MissingRequiredPackageException($packages[6], 'handle0', ['99.0', '99.1']),
                ],
            ],
            [
                $packages[6],
                [
                    $packages[0],
                ],
                [
                    new VersionMismatchException($packages[6], $packages[0], ['99.0', '99.1']),
                ],
            ],
        ];
    }

    /**
     * @dataProvider testForInstallProvider
     *
     * @param Package $package
     * @param array $installedPackages
     * @param array $expectedErrors
     */
    public function testTestForInstall(Package $package, array $installedPackages, array $expectedErrors = [])
    {
        self::$checker->setInstalledPackages($installedPackages);
        $errors = self::$checker->testForInstall($package)->getList();
        $this->assertEquals($expectedErrors, $errors);
    }

    public function testForUninstallProvider()
    {
        $packages = [
            self::createPackage('handle0', '0.1', 'Name 0', []),
            self::createPackage('handle1', '1.1', 'Name 1', ['handle0' => false]),
            self::createPackage('handle2', '2.1', 'Name 2', ['handle0' => true]),
            self::createPackage('handle3', '3.1', 'Name 3', ['handle0' => '0.1']),
        ];

        return [
            [
                $packages[0],
                [],
            ],
            [
                $packages[0],
                [
                    $packages[1],
                ],
            ],
            [
                $packages[0],
                [
                    $packages[2],
                ],
                [
                    new RequiredPackageException($packages[0], $packages[2]),
                ],
            ],
            [
                $packages[0],
                [
                    $packages[3],
                ],
                [
                    new RequiredPackageException($packages[0], $packages[3]),
                ],
            ],
        ];
    }

    /**
     * @dataProvider testForUninstallProvider
     *
     * @param Package $package
     * @param array $otherInstalledPackages
     * @param array $expectedErrors
     */
    public function testTestForUninstall(Package $package, array $otherInstalledPackages, array $expectedErrors = [])
    {
        self::$checker->setInstalledPackages(array_merge([$package], $otherInstalledPackages));
        $errors = self::$checker->testForUninstall($package)->getList();
        $this->assertEquals($expectedErrors, $errors);
    }

    /**
     * @param string $handle
     * @param string $version
     * @param string $name
     * @param array $packageDependencies
     *
     * @return Package
     */
    private function createPackage($handle, $version, $name, array $packageDependencies)
    {
        $package = $this->getMockForAbstractClass(Package::class, [], '', false);
        $reflectionClass = new ReflectionClass($package);
        foreach ([
            'pkgHandle' => $handle,
            'pkgVersion' => $version,
            'pkgName' => $name,
            'packageDependencies' => $packageDependencies,
        ] as $propertyName => $propertyValue) {
            try {
                $reflectionProperty = $reflectionClass->getProperty($propertyName);
            } catch (ReflectionException $x) {
                $reflectionProperty = null;
            }
            if ($reflectionProperty === null) {
                $package->$propertyName = $propertyValue;
            } else {
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($package, $propertyValue);
            }
        }

        return $package;
    }
}

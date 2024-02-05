<?php

namespace Concrete\Tests\Database\EntityManager\Provider;

use Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures\PackageControllerDefault;
use Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures\PackageControllerDefaultWithAdditionalNamespaces;
use Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures\PackageControllerLegacy;
use Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures\PackageControllerWithgetPackageEntityPath;
use Concrete\TestHelpers\Database\Traits\DirectoryHelpers;
use Illuminate\Filesystem\Filesystem;
use Concrete\Tests\TestCase;

/**
 * PackageProviderFactoryTest.
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class DefaultPackageProviderTest extends TestCase
{
    use DirectoryHelpers;

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Set up.
     */
    public function setUp():void
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
        $this->filesystem = new Filesystem();
    }

    /**
     * Test packages with removed getPackageEntityPath() method.
     *
     * @covers \Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithGetPackageEntityPath()
    {
        $package = new PackageControllerWithgetPackageEntityPath($this->app);
        $this->createPackageFolderOfTestMetadataDriverLegacy($package->getPackageHandle());

        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();
        self::assertIsArray($drivers);
        $c5Driver = $drivers[0] ?? null;
        self::assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        self::assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $c5Driver->getDriver());
        self::assertEquals($package->getNamespace() . '\Src', $c5Driver->getNamespace());
    }

    /**
     * Test package with default driver and not existing source directory.
     *
     * @covers \Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithNoExistingSrcDirectory()
    {
        $package = new PackageControllerDefault($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();
        self::assertIsArray($drivers);
        self::assertCount(0, $drivers);
    }

    /**
     * Covers real word case of a package with $appVersionRequired < 8.0.0.
     *
     * @covers \Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithPackageWithLegacyNamespaceAndLegacyAnnotationReader()
    {
        $this->createPackageFolderOfTestMetadataDriverLegacy();
        $package = new PackageControllerLegacy($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();
        self::assertIsArray($drivers);
        $c5Driver = $drivers[0];
        self::assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        self::assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $c5Driver->getDriver());
        self::assertEquals($package->getNamespace() . '\Src', $c5Driver->getNamespace());

        $this->removePackageFolderOfTestMetadataDriverLegacy();
    }

    /**
     * Covers real word case of a package with $appVersionRequired >= 8.0.0.
     *
     * @covers \Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithPackageWithDefaultNamespaceAndDefaultAnnotationReader()
    {
        $this->createPackageFolderOfTestMetadatadriverDefault();

        $package = new PackageControllerDefault($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();
        self::assertIsArray($drivers);
        $c5Driver = $drivers[0];
        self::assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver);
        self::assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $c5Driver->getDriver());
        self::assertEquals($package->getNamespace() . '\Entity', $c5Driver->getNamespace());

        $this->removePackageFolderOfTestMetadataDriverDefault();
    }

    /**
     * Covers package with additional namespaces and with $appVersionRewuired >= 8.0.0.
     *
     * @covers \Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithPackageWithAdditionalNamespaces()
    {
        $this->createPackageFolderOfTestMetadataDriverAdditionalNamespace();

        $package = new PackageControllerDefaultWithAdditionalNamespaces($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $drivers = $dpp->getDrivers();
        self::assertIsArray($drivers);
        self::assertCount(3, $drivers, 'Not all MappingDrivers have bin loaded');
        $c5Driver1 = $drivers[1];
        $driver1 = $c5Driver1->getDriver();
        self::assertInstanceOf('Concrete\Core\Database\EntityManager\Driver\Driver', $c5Driver1);
        self::assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $driver1);
        self::assertEquals('PortlandLabs\Concrete5\MigrationTool', $c5Driver1->getNamespace());

        $pathsOfDriver1 = $driver1->getPaths();
        self::assertEquals('src/PortlandLabs/Concrete5/MigrationTool', $this->folderPathCleaner($pathsOfDriver1[0], 4));

        $this->removePackageFolderOfTestMetadataDriverAdditionalNamespace();
    }

    /**
     * Clean up if a Exception is thrown.
     *
     * @param \Exception $e
     */
    protected function onNotSuccessfulTest(\Throwable $e): never
    {
        $this->removePackageFolderOfTestMetadataDriverDefault();
        $this->removePackageFolderOfTestMetadataDriverAdditionalNamespace();
        parent::onNotSuccessfulTest($e);
    }

    private function createPackageFolderOfTestMetadataDriverAdditionalNamespace()
    {
        $base = DIR_BASE . '/' . DIRNAME_PACKAGES . '/test_metadatadriver_additional_namespace/';
        $this->filesystem->makeDirectory($base, 0755, false, true);
        $this->filesystem->makeDirectory($base . DIRNAME_CLASSES, 0755, false, true);
        $this->filesystem->makeDirectory($base . DIRNAME_CLASSES . '/Concrete', 0755, false, true);
        $this->filesystem->makeDirectory($base . DIRNAME_CLASSES . '/Concrete/' . DIRNAME_ENTITIES, 0755, false, true);
    }

    private function removePackageFolderOfTestMetadataDriverAdditionalNamespace()
    {
        $packagePath = DIR_BASE . '/' .
            DIRNAME_PACKAGES .'/test_metadatadriver_additional_namespace';
        if ($this->filesystem->isDirectory($packagePath)) {
            $this->filesystem->deleteDirectory(DIR_BASE . '/' .
                DIRNAME_PACKAGES .
                '/test_metadatadriver_additional_namespace');
        }
    }

    private function createPackageFolderOfTestMetadatadriverDefault()
    {
        $this->filesystem->makeDirectory(DIR_BASE . '/' .
                DIRNAME_PACKAGES .
                '/test_metadatadriver_default');
        $this->filesystem->makeDirectory(DIR_BASE . '/' .
                DIRNAME_PACKAGES .
                '/test_metadatadriver_default/' .
                DIRNAME_CLASSES);
        $this->filesystem->makeDirectory(DIR_BASE . '/' .
                DIRNAME_PACKAGES .
                '/test_metadatadriver_default/' .
                DIRNAME_CLASSES .
                '/Concrete');
        $this->filesystem->makeDirectory(DIR_BASE . '/' .
                DIRNAME_PACKAGES .
                '/test_metadatadriver_default/' .
                DIRNAME_CLASSES .
                '/Concrete/' . DIRNAME_ENTITIES);
    }

    private function removePackageFolderOfTestMetadataDriverDefault()
    {
        $packagePath = DIR_BASE . '/' .
                DIRNAME_PACKAGES .
                '/test_metadatadriver_default';

        if ($this->filesystem->isDirectory($packagePath)) {
            $this->filesystem->deleteDirectory($packagePath);
        }
    }

    private function createPackageFolderOfTestMetadataDriverLegacy($dirName = 'test_metadatadriver_legacy')
    {
        $baseDir = DIR_BASE . '/' . DIRNAME_PACKAGES;
        $this->filesystem->makeDirectory($baseDir . '/' . $dirName);
        $this->filesystem->makeDirectory($baseDir . '/' . $dirName . '/' . DIRNAME_CLASSES);
    }

    private function removePackageFolderOfTestMetadataDriverLegacy()
    {
        $basePath = DIR_BASE . '/' . DIRNAME_PACKAGES;
        $packagePaths = [
            $basePath . '/test_metadatadriver_legacy',
            $basePath . '/test_metadatadriver_legacy_with_getpackageentitypath',
        ];

        foreach ($packagePaths as $packagePath) {
            if ($this->filesystem->isDirectory($packagePath)) {
                $this->filesystem->deleteDirectory($packagePath);
            }
        }
    }

    /**
     * @after
     */
    public function after()
    {
        $this->removePackageFolderOfTestMetadataDriverLegacy();
        $this->removePackageFolderOfTestMetadataDriverDefault();
        $this->removePackageFolderOfTestMetadataDriverAdditionalNamespace();
    }
}

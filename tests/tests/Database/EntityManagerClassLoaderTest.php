<?php

declare(strict_types=1);

namespace Concrete\Tests\Database;

use Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory;
use Concrete\Core\Database\EntityManagerConfigFactory;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Package\Package;
use Concrete\Tests\TestCase;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Illuminate\Filesystem\Filesystem;

defined('C5_EXECUTE') or die('Access Denied.');

class EntityManagerClassLoaderTest extends TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * The absolute paths of the packages created for testing purposes.
     *
     * @var string[]
     */
    protected $createdTestPackages = [];

    /**
     * Should the class autoloader be reset when the test ends?
     *
     * @var bool
     */
    protected $classLoaderNeedsReset = false;

    public function setUp(): void
    {
        parent::setUp();
        $this->app = app();
    }

    public function tearDown(): void
    {
        $filesystem = new Filesystem();
        foreach ($this->createdTestPackages as $packagePath) {
            $filesystem->deleteDirectory($packagePath);
        }
        $this->createdTestPackages = [];
        if ($this->classLoaderNeedsReset) {
            $this->classLoaderNeedsReset = false;
            ClassLoader::getInstance()->reset();
        }
        parent::tearDown();
    }

    public function testCoreEntityClasses(): void
    {
        $classExists = class_exists(\Concrete\Core\Entity\Site\Site::class);
        static::assertTrue($classExists);

        $entityManagerConfigFactory = $this->app->make(EntityManagerConfigFactory::class);
        $driverChain = $entityManagerConfigFactory->getMetadataDriverImpl();
        $drivers = $driverChain->getDrivers();

        static::assertArrayHasKey('Concrete\Core\Entity', $drivers);

        // Test if the correct MetadataDriver and MetadataReader are present
        $defaultAnnotationDriver = $drivers['Concrete\Core\Entity'];

        // Test if the driver contains the default lookup path
        $driverPaths = $defaultAnnotationDriver->getPaths();
        static::assertEquals(
            DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
            $driverPaths[0]
        );
    }

    public function testApplicationEntityClasses(): void
    {
        $entitiesDir = dirname(DIR_BASE_CORE) . '/application/src/Entity';
        static::assertFalse(is_dir($entitiesDir));
        static::assertTrue(@mkdir("{$entitiesDir}/Advertisement", 0777, true));
        try {
            static::assertNotFalse(
                file_put_contents("{$entitiesDir}/Advertisement/BannerAd.php", $this->buildEntityCode('Application\Entity\Advertisement', 'BannerAd'))
            );
            $classExists = class_exists('Application\Entity\Advertisement\BannerAd');
            static::assertTrue($classExists);

            $entityManagerConfigFactory = $this->app->make(EntityManagerConfigFactory::class);
            $driverChain = $entityManagerConfigFactory->getMetadataDriverImpl();
            $drivers = $driverChain->getDrivers();

            static::assertArrayHasKey('Application\Entity', $drivers);

            // Test if the correct MetadataDriver and MetadataReader are present
            $defaultAnnotationDriver = $drivers['Application\Entity'];

            // Test if the driver contains the default lookup path
            $driverPaths = $defaultAnnotationDriver->getPaths();
            static::assertEquals(
                DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
                $driverPaths[0]
            );
        } finally {
            $fs = new Filesystem();
            $fs->deleteDirectory($entitiesDir);
        }
    }

    /**
     * packages/your_package/src/Concrete/Entity mapped to Concrete\Package\YourPackage\Entity.
     */
    public function testPackageStandardEntityLocation(): void
    {
        $handle = 'test_em_standard_location';
        $namespace = 'Concrete\Package\TestEmStandardLocation';
        $packagePath = $this->createTestPackage($handle, [
            FILENAME_PACKAGE_CONTROLLER => $this->buildPackageControllerCode($namespace, $handle, '9.0.0'),
            DIRNAME_CLASSES . '/Concrete/' . DIRNAME_ENTITIES . '/Widget.php' => $this->buildEntityCode($namespace . '\Entity', 'Widget'),
        ]);
        $package = $this->registerTestPackage($handle, $namespace);

        static::assertTrue(class_exists($namespace . '\Entity\Widget'), 'The package entity class has not been autoloaded.');

        $drivers = $this->getPackageDrivers($package);
        static::assertCount(1, $drivers);
        static::assertEquals($namespace . '\Entity', $drivers[0]->getNamespace());
        static::assertInstanceOf(AnnotationDriver::class, $drivers[0]->getDriver());
        static::assertEquals(
            [$packagePath . '/' . DIRNAME_CLASSES . '/Concrete/' . DIRNAME_ENTITIES],
            $drivers[0]->getDriver()->getPaths()
        );
    }

    /**
     * packages/your_package/src/Something/Something/Entity mapped to Something\Something\Entity.
     */
    public function testPackageCustomEntityLocation(): void
    {
        $handle = 'test_em_custom_location';
        $namespace = 'Concrete\Package\TestEmCustomLocation';
        $packagePath = $this->createTestPackage($handle, [
            FILENAME_PACKAGE_CONTROLLER => $this->buildPackageControllerCode($namespace, $handle, '9.0.0', [
                'pkgAutoloaderRegistries' => [DIRNAME_CLASSES . '/Acme/Blog' => '\Acme\Blog'],
            ]),
            DIRNAME_CLASSES . '/Acme/Blog/' . DIRNAME_ENTITIES . '/Post.php' => $this->buildEntityCode('Acme\Blog\Entity', 'Post'),
        ]);
        $package = $this->registerTestPackage($handle, $namespace);

        static::assertTrue(class_exists('Acme\Blog\Entity\Post'), 'The package entity class has not been autoloaded.');

        $drivers = $this->getPackageDrivers($package);
        // There's no src/Concrete/Entity directory: the only driver is the one of the custom namespace.
        static::assertCount(1, $drivers);
        // The leading backslash of the configured namespace must be stripped off.
        static::assertEquals('Acme\Blog', $drivers[0]->getNamespace());
        static::assertInstanceOf(AnnotationDriver::class, $drivers[0]->getDriver());
        static::assertEquals(
            [$packagePath . '/' . DIRNAME_CLASSES . '/Acme/Blog'],
            $drivers[0]->getDriver()->getPaths()
        );
    }

    /**
     * application/src mapped to Application\Src (only when the legacy namespace is enabled).
     */
    public function testLegacyApplicationSrcLocation(): void
    {
        $config = $this->app->make('config');
        $config->withKey('app.enable_legacy_src_namespace', true, function () {
            $entityManagerConfigFactory = $this->app->make(EntityManagerConfigFactory::class);
            $drivers = $entityManagerConfigFactory->getMetadataDriverImpl()->getDrivers();
            static::assertArrayNotHasKey('Application\Entity', $drivers);
            static::assertArrayHasKey('Application\Src', $drivers);
            $driver = $drivers['Application\Src'];
            static::assertInstanceOf(AnnotationDriver::class, $driver);
            static::assertEquals([DIR_APPLICATION . '/' . DIRNAME_CLASSES], $driver->getPaths());
        });
    }

    /**
     * packages/your_package/src mapped to Concrete\Package\YourPackage\Src.
     */
    public function testLegacyPackageSrcLocation(): void
    {
        $handle = 'test_em_legacy_location';
        $namespace = 'Concrete\Package\TestEmLegacyLocation';
        $packagePath = $this->createTestPackage($handle, [
            FILENAME_PACKAGE_CONTROLLER => $this->buildPackageControllerCode($namespace, $handle, '5.7.5', [
                'pkgEnableLegacyNamespace' => true,
            ]),
            DIRNAME_CLASSES . '/Model/Widget.php' => $this->buildEntityCode($namespace . '\Src\Model', 'Widget'),
        ]);
        $package = $this->registerTestPackage($handle, $namespace);
        static::assertTrue($package->shouldEnableLegacyNamespace());

        static::assertTrue(class_exists($namespace . '\Src\Model\Widget'), 'The package entity class has not been autoloaded.');

        $drivers = $this->getPackageDrivers($package);
        static::assertCount(1, $drivers);
        static::assertEquals($namespace . '\Src', $drivers[0]->getNamespace());
        static::assertInstanceOf(AnnotationDriver::class, $drivers[0]->getDriver());
        static::assertEquals([$packagePath . '/' . DIRNAME_CLASSES], $drivers[0]->getDriver()->getPaths());
    }

    /**
     * Get the entity metadata drivers of a package.
     *
     * @return \Concrete\Core\Database\EntityManager\Driver\Driver[]
     */
    protected function getPackageDrivers(Package $package): array
    {
        $providerFactory = new PackageProviderFactory($this->app, $package);

        return array_values($providerFactory->getEntityManagerProvider()->getDrivers());
    }

    /**
     * Create a package in the packages directory (it's automatically deleted when the test ends).
     *
     * @param string $handle the handle of the package
     * @param array $files the contents of the files to be created, with keys being the paths relative to the package directory
     *
     * @return string the absolute path of the created package directory
     */
    protected function createTestPackage(string $handle, array $files): string
    {
        $packagePath = DIR_PACKAGES . '/' . $handle;
        if (is_dir($packagePath)) {
            static::fail("The {$packagePath} directory already exists: please remove it in order to run this test.");
        }
        $this->createdTestPackages[] = $packagePath;
        foreach ($files as $file => $contents) {
            $path = $packagePath . '/' . $file;
            $directory = dirname($path);
            if (!is_dir($directory) && !@mkdir($directory, 0777, true)) {
                static::fail("Failed to create the {$directory} directory.");
            }
            if (@file_put_contents($path, $contents) === false) {
                static::fail("Failed to create the {$path} file.");
            }
        }

        return $packagePath;
    }

    /**
     * Let the class autoloader know about a package created with createTestPackage(), and build its controller.
     *
     * @param string $handle the handle of the package
     * @param string $namespace the namespace of the package
     */
    protected function registerTestPackage(string $handle, string $namespace): Package
    {
        ClassLoader::getInstance()->registerPackage($handle);
        $this->classLoaderNeedsReset = true;
        $controllerClass = $namespace . '\Controller';
        static::assertTrue(class_exists($controllerClass), 'The package controller class has not been autoloaded.');
        $package = new $controllerClass($this->app);
        // Register the package controller too: the location of the entity classes depends on it.
        ClassLoader::getInstance()->registerPackage($package);

        return $package;
    }

    /**
     * Build the PHP code of the controller of a test package.
     *
     * @param string $namespace the namespace of the package
     * @param string $handle the handle of the package
     * @param string $appVersionRequired the minimum core version required by the package
     * @param array $extraProperties additional protected properties of the package controller
     */
    protected function buildPackageControllerCode(string $namespace, string $handle, string $appVersionRequired, array $extraProperties = []): string
    {
        $properties = $extraProperties + [
            'pkgHandle' => $handle,
            'appVersionRequired' => $appVersionRequired,
            'pkgVersion' => '1.0.0',
        ] + $extraProperties;
        $propertiesCode = '';
        foreach ($properties as $name => $value) {
            $propertiesCode .= "    protected \${$name} = " . var_export($value, true) . ";\n";
        }

        return <<<EOT
        <?php
        namespace {$namespace};
        use Concrete\Core\Package\Package;
        class Controller extends Package
        {
        {$propertiesCode}
            public function getPackageName()
            {
                return '{$handle}';
            }
            public function getPackageDescription()
            {
                return '{$handle}';
            }
        }
        EOT;
    }

    /**
     * Build the PHP code of an entity class of a test package.
     *
     * @param string $namespace the namespace of the entity class
     * @param string $className the short name of the entity class
     */
    protected function buildEntityCode(string $namespace, string $className): string
    {
        return <<<EOT
        <?php
        namespace {$namespace};
        class {$className}
        {
        }
        EOT;
    }
}

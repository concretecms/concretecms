<?php

namespace Concrete\Tests\Database;

use PHPUnit_Framework_TestCase;

class EntityManagerClassLoaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
    }

    public function testCoreEntityClasses()
    {
        $classExists = class_exists('Concrete\Core\Entity\Site\Site');
        $this->assertTrue($classExists);

        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $driverChain = $entityManagerConfigFactory->getMetadataDriverImpl();
        $drivers = $driverChain->getDrivers();

        $this->assertArrayHasKey('Concrete\Core\Entity', $drivers);

        // Test if the correct MetadataDriver and MetadataReader are present
        $defaultAnnotationDriver = $drivers['Concrete\Core\Entity'];

        // Test if the driver contains the default lookup path
        $driverPaths = $defaultAnnotationDriver->getPaths();
        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
            $driverPaths[0]);
    }

    public function testApplicationEntityClasses()
    {
        $root = dirname(DIR_BASE_CORE . '../');
        @mkdir($root . '/application/src/Entity/Advertisement', 0777, true);
        copy(DIR_TESTS . '/assets/Database/BannerAd.php', $root . '/application/src/Entity/Advertisement/BannerAd.php');

        $classExists = class_exists('Application\Entity\Advertisement\BannerAd');

        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $driverChain = $entityManagerConfigFactory->getMetadataDriverImpl();
        $drivers = $driverChain->getDrivers();

        $this->assertArrayHasKey('Application\Entity', $drivers);

        // Test if the correct MetadataDriver and MetadataReader are present
        $defaultAnnotationDriver = $drivers['Application\Entity'];

        // Test if the driver contains the default lookup path
        $driverPaths = $defaultAnnotationDriver->getPaths();
        $this->assertEquals(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
            $driverPaths[0]);

        unlink($root . '/application/src/Entity/Advertisement/BannerAd.php');
        rmdir($root . '/application/src/Entity/Advertisement');
        rmdir($root . '/application/src/Entity');

        $this->assertTrue($classExists);
    }

    /**
     * packages/your_package/src/Entity, v8.
     * 1. MAke sure to test directory
     * 2. Make sure to test annotation paths
     * 3. Make sure to test package version minimum
     * 4. Make sure to test the annotation driver somehow so we can verify it's importing ORM I guess?
     */
    public function testPackageStandardEntityLocation()
    {
    }

    /**
     * packages/your_package/src/Something/Something/Entity, maps to \Something\Something\Entity.
     */
    public function testPackageCustomEntityLocation()
    {
    }

    public function testLegacyApplicationSrcLocation()
    {
    }

    public function testLegacyPackageSrcLocation()
    {
    }
}

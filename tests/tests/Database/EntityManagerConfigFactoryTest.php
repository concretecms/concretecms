<?php

namespace Concrete\Tests\Database;

use Concrete\Core\Support\Facade\Application;
use PHPUnit_Framework_TestCase;

/**
 * EntityManagerConfigFactoryTest.
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class EntityManagerConfigFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
    }

    /**
     * Test the default metadata implementation for the Core classes.
     */
    public function testGetConfigurationDefaultSettingsForTheCore()
    {
        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $driverChain = $entityManagerConfigFactory->getMetadataDriverImpl();
        $this->assertInstanceOf('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain',
            $driverChain, 'Is not a Doctrine ORM MappingDriverChain');

        // mitgrated to Database/EntityManager/Driver/CoreDriverTest
        //
//        $drivers                    = $driverChain->getDrivers();
//
//        $this->assertArrayHasKey('Concrete\Core\Entity', $drivers);
//
//        // Test if the correct MetadataDriver and MetadataReader are present
//        $defaultAnnotationDriver = $drivers['Concrete\Core\Entity'];
//        $defaultAnnotationReader = $defaultAnnotationDriver->getReader();
//        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver',
//            $defaultAnnotationDriver,
//            'The core metadata driver musst be an AnnotationDriver');
//        $this->assertInstanceOf('Doctrine\Common\Annotations\CachedReader',
//            $defaultAnnotationReader,
//            'AnnotationReader is not cached. For performance reasons, it should be wrapped in a CachedReader');
//
//        // Test if the driver contains the default lookup path
//        $driverPaths = $defaultAnnotationDriver->getPaths();
//        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_CLASSES.'/'.DIRNAME_ENTITIES,
//            $driverPaths[0]);
    }

    /**
     * Test the default metadata implementation for the Application classes.
     */
    public function testGetConfigurationDefaultSettingsForTheApplication()
    {
        $root = dirname(DIR_BASE_CORE . '../');
        mkdir($root . '/application/src/Entity', 0777, true);

        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $driverChain = $entityManagerConfigFactory->getMetadataDriverImpl();
        $this->assertInstanceOf('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain',
            $driverChain, 'Is not a Doctrine ORM MappingDriverChain');
        $drivers = $driverChain->getDrivers();
        $this->assertArrayHasKey('Application\Entity', $drivers);

        // Test if the correct MetadataDriver and MetadataReader are present
        $defaultAnnotationDriver = $drivers['Application\Entity'];
        $defaultAnnotationReader = $defaultAnnotationDriver->getReader();
//        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver',
//            $defaultAnnotationDriver);
        $this->assertInstanceOf('Doctrine\Common\Annotations\CachedReader',
            $defaultAnnotationReader,
            'AnnotationReader is not cached. For performance reasons, it should be wrapped in a CachedReader');

        // Test if the driver contains the default lookup path
        $driverPaths = $defaultAnnotationDriver->getPaths();
        $this->assertEquals(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
            $driverPaths[0]);

        rmdir($root . '/application/src/Entity');
    }

    public function testGetConfigurationDefaultSettingsForTheApplicationWithLegacyOption()
    {
        \Config::save('app.enable_legacy_src_namespace', true);

        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $driverChain = $entityManagerConfigFactory->getMetadataDriverImpl();
        $drivers = $driverChain->getDrivers();
        $this->assertArrayHasKey('Application\Src', $drivers);

        // Test if the correct MetadataDriver and MetadataReader are present
        $defaultAnnotationDriver = $drivers['Application\Src'];
        $defaultAnnotationReader = $defaultAnnotationDriver->getReader();
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver',
            $defaultAnnotationDriver);
        $this->assertInstanceOf('Doctrine\Common\Annotations\CachedReader',
            $defaultAnnotationReader,
            'AnnotationReader is not cached. For performance reasons, it should be wrapped in a CachedReader');

        // Test if the driver contains the default lookup path
        $driverPaths = $defaultAnnotationDriver->getPaths();
        $this->assertEquals(DIR_APPLICATION . '/' . DIRNAME_CLASSES,
            $driverPaths[0]);

        \Config::save('app.enable_legacy_src_namespace', false);
    }

    /**
     * Test the metadata implementation for entities located under application/src/Entity with YAML driver
     * In this case the folder application/config/xml is not present so it will fallback to default.
     *
     * @dataProvider dataProviderGetConfigurationWithApplicationYmlDriver
     *
     * @param string|int $setting
     */
    public function testGetConfigurationWithApplicationYmlDriverFallbackToDefault($setting)
    {
        $entityManagerConfigFactory = $this->getEntityManagerFactoryWithStubConfigRepository($setting);

        // Test if the correct MetadataDriver and MetadataReader are present
        $drivers = $entityManagerConfigFactory->getMetadataDriverImpl()->getDrivers();

        $this->assertArrayHasKey('Application\Entity', $drivers);
        $defaultAnnotationDriver = $drivers['Application\Entity'];
        $defaultAnnotationReader = $defaultAnnotationDriver->getReader();
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver',
            $defaultAnnotationDriver);
        $this->assertInstanceOf('Doctrine\Common\Annotations\CachedReader',
            $defaultAnnotationReader,
            'AnnotationReader is not cached. For performance reasons, it should be wrapped in a CachedReader');

        // Test if the driver contains the default lookup path
        $driverPaths = $defaultAnnotationDriver->getPaths();
        $this->assertEquals(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
            $driverPaths[0]);
    }

    public function dataProviderGetConfigurationWithApplicationYmlDriver()
    {
        return [
            ['yml'],
            ['yaml'],
        ];
    }

    /**
     * Test the metadata implementation for entities located under application/src with Xml driver
     * In this case the folder application/config/xml is not present so it will fallback to default.
     *
     * @dataProvider dataProviderGetConfigurationWithApplicationXmlDriver
     *
     * @param string|int $setting
     */
    public function testGetConfigurationWithApplicationXmlDriverFallbackToDefault($setting)
    {
        $entityManagerConfigFactory = $this->getEntityManagerFactoryWithStubConfigRepository($setting);

        // Test if the correct MetadataDriver and MetadataReader are present
        $drivers = $entityManagerConfigFactory->getMetadataDriverImpl()->getDrivers();
        $this->assertArrayHasKey('Application\Entity', $drivers);
        $defaultAnnotationDriver = $drivers['Application\Entity'];
        $defaultAnnotationReader = $defaultAnnotationDriver->getReader();
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver',
            $defaultAnnotationDriver);
        $this->assertInstanceOf('Doctrine\Common\Annotations\CachedReader',
            $defaultAnnotationReader,
            'AnnotationReader is not cached. For performance reasons, it should be wrapped in a CachedReader');

        // Test if the driver contains the default lookup path
        $driverPaths = $defaultAnnotationDriver->getPaths();
        $this->assertEquals(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
            $driverPaths[0]);
    }

    public function dataProviderGetConfigurationWithApplicationXmlDriver()
    {
        return [
            ['xml'],
        ];
    }

    /**
     * Create the EntityManagerFactory with stub ConfigRepository option.
     *
     * @param array $setting with data from the dataProvider
     *
     * @return \Concrete\Core\Database\EntityManagerConfigFactory
     */
    protected function getEntityManagerFactoryWithStubConfigRepository($setting)
    {
        $config = $this->app->make('Doctrine\ORM\Configuration');
        $configRepoStub = $this->getMockBuilder('Concrete\Core\Config\Repository\Repository')
                                ->disableOriginalConstructor()
                                ->getMock();
        $configRepoStub->method('get')
            ->will($this->onConsecutiveCalls(
                    false,
                    [],
                    false,
                    $setting
                ));
        $entityManagerConfigFactory = new \Concrete\Core\Database\EntityManagerConfigFactory($this->app, $config, $configRepoStub);

        return $entityManagerConfigFactory;
    }

    // Test a package with no classes
}

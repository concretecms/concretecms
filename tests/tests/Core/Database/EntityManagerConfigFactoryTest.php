<?php

namespace Concrete\Tests\Core\Database;

use Concrete\Core\Support\Facade\Application;

/**
 * EntityManagerConfigFactoryTest
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class EntityManagerConfigFactoryTest extends \PHPUnit_Framework_TestCase
{     
    /**
     * @var \Concrete\Core\Application\Application 
     */
    protected $app;
    
    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();
        $this->app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
    }

    /**
     * Test the default metadata implementation for the Core classes
     */
    public function testGetConfigurationDefaultSettingsForTheCore()
    {
        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $driverChain                = $entityManagerConfigFactory->getMetadataDriverImpl();
        $this->assertInstanceOf('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain',
            $driverChain, 'Is not a Doctrine ORM MappingDriverChain');
        $drivers                    = $driverChain->getDrivers();
        $this->assertArrayHasKey('Concrete\Core', $drivers);

        // Test if the correct MetadataDriver and MetadataReader are present
        $defaultAnnotationDriver = $drivers['Concrete\Core'];
        $defaultAnnotationReader = $defaultAnnotationDriver->getReader();
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver',
            $defaultAnnotationDriver,
            'The core metadata driver musst be an AnnotationDriver');
        $this->assertInstanceOf('Doctrine\Common\Annotations\CachedReader',
            $defaultAnnotationReader,
            'AnnotationReader is not cached. For performance reasons, it should be wrapped in a CachedReader');

        // Test if the driver contains the default lookup path
        $driverPaths = $defaultAnnotationDriver->getPaths();
        $this->assertEquals(DIR_BASE_CORE.DIRECTORY_SEPARATOR.DIRNAME_CLASSES.'/'.DIRNAME_ENTITIES,
            $driverPaths[0]);
    }

    /**
     * Test the default metadata implementation for the Application classes
     */
    public function testGetConfigurationDefaultSettingsForTheApplication()
    {
        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $driverChain                = $entityManagerConfigFactory->getMetadataDriverImpl();
        $this->assertInstanceOf('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain',
            $driverChain, 'Is not a Doctrine ORM MappingDriverChain');
        $drivers                    = $driverChain->getDrivers();
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
        $this->assertEquals(DIR_APPLICATION.DIRECTORY_SEPARATOR.DIRNAME_CLASSES,
            $driverPaths[0]);
    }

    /**
     * Test the metadata implementation for the Application classes with YAML driver
     *
     * @dataProvider dataProviderGetConfigurationWithApplicationYmlDriver
     * 
     * @param string|integer $setting
     */
    public function testGetConfigurationWithApplicationYmlDriver($setting)
    {

        $entityManagerConfigFactory = $this->getEntityManagerFactoryWithStubConfigRepository($setting);

        // Test if the correct MetadataDriver and MetadataReader are present
        $drivers = $entityManagerConfigFactory->getMetadataDriverImpl()->getDrivers();
        $this->assertArrayHasKey('Application\Src', $drivers);
        $driver  = $drivers['Application\Src'];
        $this->assertInstanceOf('\Doctrine\ORM\Mapping\Driver\YamlDriver',
            $driver);

        // Test if the driver contains the default lookup path
        $driverPaths = $driver->getLocator()->getPaths();
        $this->assertEquals(DIR_APPLICATION.DIRECTORY_SEPARATOR.REL_DIR_METADATA_YAML,
            $driverPaths[0]);
    }

    public function dataProviderGetConfigurationWithApplicationYmlDriver()
    {
        return array(
            array('yml'),
            array('yaml'),
            array(3), // equals \Package::PACKAGE_METADATADRIVER_YAML
        );
    }

    /**
     * Test the metadata implementation for the Application classes with Xml driver
     *
     * @dataProvider dataProviderGetConfigurationWithApplicationXmlDriver
     * 
     * @param string|integer $setting
     */
    public function testGetConfigurationWithApplicationXmlDriver($setting)
    {

        $entityManagerConfigFactory = $this->getEntityManagerFactoryWithStubConfigRepository($setting);

        // Test if the correct MetadataDriver and MetadataReader are present
        $drivers = $entityManagerConfigFactory->getMetadataDriverImpl()->getDrivers();
        $this->assertArrayHasKey('Application\Src', $drivers);
        $driver  = $drivers['Application\Src'];
        $this->assertInstanceOf('\Doctrine\ORM\Mapping\Driver\XmlDriver',
            $driver);

        // Test if the driver contains the default lookup path
        $driverPaths = $driver->getLocator()->getPaths();
        $this->assertEquals(DIR_APPLICATION.DIRECTORY_SEPARATOR.REL_DIR_METADATA_XML,
            $driverPaths[0]);
    }

    public function dataProviderGetConfigurationWithApplicationXmlDriver()
    {
        return array(
            array('xml'),
            array(2), // equals \Package::PACKAGE_METADATADRIVER_XML
        );
    }

    /**
     * Create the EntityManagerFactory with stub ConfigRepository option
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
                    array(),
                    $setting
                ));
        $entityManagerConfigFactory = new \Concrete\Core\Database\EntityManagerConfigFactory($this->app, $config, $configRepoStub);

        return $entityManagerConfigFactory;
    }
}
<?php

namespace Concrete\Tests\Core\Database;

use Concrete\Core\Support\Facade\Application;
use Illuminate\Filesystem\Filesystem;

/**
 * ApplicationXMLMetadataDriverTest
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class ApplicationYmlMetadataDriverTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Support\Facade\Application
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
     * Creaete the xml folder in application/conifg
     */
    public static function setUpBeforeClass()
    {   
        
        $filesystem = new Filesystem();
        if (!$filesystem->isWritable(DIR_APPLICATION . DIRECTORY_SEPARATOR . 'config')) {
            throw new \Exception("Cannot write to the application config directory for the testing purposes. Please check permissions!");
        }
        
        if(!$filesystem->isDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML)){
            $filesystem->makeDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML);
        }
    }
    
    /**
     * Test the metadata implementation for entities located under application/src with YAML driver
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
        $this->assertEquals(DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML,
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
    
    public function tearDown()
    {
        parent::tearDown();
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
    
    /**
     * Delete the xml folder in application/conifg
     */
    public static function tearDownAfterClass()
    {   
        $filesystem = new Filesystem();
        if($filesystem->isDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML)){
            $filesystem->deleteDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML);
        }
        parent::tearDown();
    }
}

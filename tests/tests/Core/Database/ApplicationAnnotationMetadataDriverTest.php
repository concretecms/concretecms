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
class ApplicationAnnotationMetadataDriverTest extends \PHPUnit_Framework_TestCase
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
        $this->app = Application::getFacadeApplication();
    }
    
    /**
     * Creaete the xml folder in application/conifg
     */
    public static function setUpBeforeClass()
    {   
        
        $filesystem = new Filesystem();
        if (!$filesystem->isWritable(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES)) {
            throw new \Exception("Cannot write to the application/src directory for the testing purposes. Please check permissions!");
        }
        if(!$filesystem->isDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES)){
            $filesystem->makeDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES);
        }
        if(!$filesystem->isDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES.DIRECTORY_SEPARATOR.DIRNAME_ENTITIES)){
            $filesystem->makeDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES.DIRECTORY_SEPARATOR.DIRNAME_ENTITIES);
        }
    }
    
    /**
     * Test the metadata implementation for entities located under application/src with Xml driver
     **
     * @param string|integer $setting
     */
    public function testGetConfigurationWithApplicationXmlDriver()
    {

        $entityManagerConfigFactory = $this->getEntityManagerFactoryWithStubConfigRepository();

        // Test if the correct MetadataDriver and MetadataReader are present
        $drivers = $entityManagerConfigFactory->getMetadataDriverImpl()->getDrivers();
        $this->assertArrayHasKey('Application\Entity', $drivers);
        $driver  = $drivers['Application\Entity'];
        $this->assertInstanceOf('\Doctrine\ORM\Mapping\Driver\AnnotationDriver',
            $driver);

        // Test if the driver contains the default lookup path
        $driverPaths = $driver->getPaths();
        $this->assertEquals(DIR_APPLICATION.DIRECTORY_SEPARATOR.DIRNAME_CLASSES.DIRECTORY_SEPARATOR.DIRNAME_ENTITIES,
            $driverPaths[0]);
    }

    /**
     * Create the EntityManagerFactory with stub ConfigRepository option
     *
     * @param array $setting with data from the dataProvider
     * 
     * @return \Concrete\Core\Database\EntityManagerConfigFactory
     */
    protected function getEntityManagerFactoryWithStubConfigRepository()
    {
        $config = $this->app->make('Doctrine\ORM\Configuration');
        $configRepoStub = $this->getMockBuilder('Concrete\Core\Config\Repository\Repository')
                                ->disableOriginalConstructor()
                                ->getMock();
        $configRepoStub->method('get')
            ->will($this->onConsecutiveCalls(
                    false, array()
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
        if($filesystem->isDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES.DIRECTORY_SEPARATOR.DIRNAME_ENTITIES)){
            $filesystem->deleteDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES.DIRECTORY_SEPARATOR.DIRNAME_ENTITIES);
        }
        parent::tearDownAfterClass();
    }
}

<?php

namespace Concrete\Tests\Core\Database\EntityManager\Driver;

use Concrete\Core\Database\EntityManager\Driver\ApplicationDriver;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Filesystem\Filesystem;

/**
 * Description of ApplicationDriverTest
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class ApplicationDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $configRepository;

    /**
     * Setup
     */
    public function setUp()
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $this->configRepository = $entityManagerConfigFactory->getConfigRepository();
    }

    /**
     * Test default application driver with
     * - empty CONFIG_ORM_METADATA_APPLICATION config setting
     * - a present application/src/Entity folder
     *
     * @covers ApplicationDriver::getDriver
     * @throws \Exception
     */
    public function testGetDefaultDriver(){

        $filesystem = new Filesystem();
        if (!$filesystem->isWritable(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES)) {
            throw new \Exception("Cannot write to the application/src directory for the testing purposes. Please check permissions!");
        }
        if(!$filesystem->isDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES)){
            $filesystem->makeDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES);
        }
        if(!$filesystem->isDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES . DIRECTORY_SEPARATOR . DIRNAME_ENTITIES)){
            $filesystem->makeDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES . DIRECTORY_SEPARATOR .DIRNAME_ENTITIES);
        }

        $reader = $this->app->make('orm/cachedAnnotationReader');
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $driver = $applicationDriver->getDriver();
        // check if its the correct driver
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $driver);
        // check if it contains the correct reader
        // Doctrine doesn't provied a way of accessing the original reader in the cached reader
        $this->assertEquals($reader, $driver->getReader());

        if($filesystem->isDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES.DIRECTORY_SEPARATOR.DIRNAME_ENTITIES)){
            $filesystem->deleteDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . DIRNAME_CLASSES.DIRECTORY_SEPARATOR.DIRNAME_ENTITIES);
        }
        if($filesystem->isDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML)){
            $filesystem->deleteDirectory(DIR_APPLICATION . DIRECTORY_SEPARATOR . REL_DIR_METADATA_YAML);
        }

    }

    /**
     * Test default application driver with no folder at application/src/Entity
     *
     * @covers ApplicationDriver::getDriver
     */
    public function testFailingGetDefaultDriverWithNoEntityFolder(){
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $driver = $applicationDriver->getDriver();
        // this case doesn't return a driver
        $this->assertNull($driver);
    }



    public function testGetLegacyDriver(){


    }
    
    public function testGetXMLDriver(){
        
    }

    public function testGetYMLDriver(){

    }



    public function dataProviderGetDriver(){

    }

    /**
     * Test namespace
     *
     * @dataProvider dataProviderGetNamespace
     *
     * @param array $settings
     */
    public function testGetNamespace($isLegacy, $namespace){
        $config = $this->app->make('config');
        if($isLegacy){
            $config->save('app.enable_legacy_src_namespace', true);
        }
        
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $this->assertEquals($namespace, $applicationDriver->getNamespace());

        if($isLegacy){
            $config->save('app.enable_legacy_src_namespace', false);
        }

    }

    /**
     * Test default and legacy namespaces of application entites
     *
     * @return array
     */
    public function dataProviderGetNamespace()
    {
        return array(
            array('isLegacy'=> true, 'namespace' => 'Application\Src'),
            array('isLegacy'=> false, 'namespace' => 'Application\Entity'),
        );
    }


}
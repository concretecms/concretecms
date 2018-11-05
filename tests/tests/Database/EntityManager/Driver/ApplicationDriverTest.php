<?php

namespace Concrete\Tests\Database\EntityManager\Driver;

use Concrete\Core\Database\EntityManager\Driver\ApplicationDriver;
use Concrete\Core\Support\Facade\Application;
use Illuminate\Filesystem\Filesystem;
use PHPUnit_Framework_TestCase;

/**
 * ApplicationDriverTest.
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class ApplicationDriverTest extends PHPUnit_Framework_TestCase
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
     * @var Illuminate\Filesystem\Filesystem;
     */
    protected $filesystem;

    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $this->configRepository = $entityManagerConfigFactory->getConfigRepository();
        $this->filesystem = new Filesystem();
    }

    /**
     * Clean up after each tests.
     */
    public function tearDown()
    {
        $this->cleanupFolderSystem();
        $this->cleanupConfig();
    }

    /**
     * Test default application driver with
     * - empty CONFIG_ORM_METADATA_APPLICATION config setting
     * - a present application/src/Entity folder.
     *
     * @covers \Concrete\Core\Database\EntityManager\Driver\ApplicationDriver::getDriver
     *
     * @throws \Exception
     */
    public function testGetDefaultDriver()
    {
        // prepare
        if (!$this->filesystem->isWritable(DIR_APPLICATION . '/' . DIRNAME_CLASSES)) {
            throw new \Exception('Cannot write to the application/src directory for the testing purposes. Please check permissions!');
        }
        if (!$this->filesystem->isDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES)) {
            $this->filesystem->makeDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES);
        }

        // test
        $reader = $this->app->make('orm/cachedAnnotationReader');
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $driver = $applicationDriver->getDriver();
        // check if its the correct driver
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $driver);
        // check if it contains the correct reader
        // Doctrine doesn't provied a way of accessing the original reader in the cached reader
        $this->assertEquals($reader, $driver->getReader(), 'The AnnotationReader is not wrapped with a CachedReader.');
    }

    /**
     * Test default application driver with no folder at application/src/Entity.
     *
     * @covers \Concrete\Core\Database\EntityManager\Driver\ApplicationDriver::getDriver
     */
    public function testFailingGetDefaultDriverWithNoEntityDirectory()
    {
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $driver = $applicationDriver->getDriver();
        // this case doesn't return a driver
        $this->assertNull($driver);
    }

    /**
     * Test application legacy driver
     * - empty CONFIG_ORM_METADATA_APPLICATION config setting
     * - a present application/src folder
     * - and config 'app.enable_legacy_src_namespace' = true.
     *
     * @covers \Concrete\Core\Database\EntityManager\Driver\ApplicationDriver::getDriver
     *
     * @throws \Exception
     */
    public function testGetLegacyDriver()
    {
        // prepare
        if (!$this->filesystem->isWritable(DIR_APPLICATION . '/' . DIRNAME_CLASSES)) {
            throw new \Exception('Cannot write to the application/src directory for the testing purposes. Please check permissions!');
        }
        $this->configRepository->set('app.enable_legacy_src_namespace', true);

        // test
        $reader = $this->app->make('orm/cachedSimpleAnnotationReader');
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $driver = $applicationDriver->getDriver();
        // check if its the correct driver
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $driver);
        // check if it contains the correct reader
        // Doctrine doesn't provied a way of accessing the original reader in the cached reader
        $this->assertEquals($reader, $driver->getReader(), 'The SimpleAnnotationReader is not wrapped with a CachedReader.');
    }

    /**
     * Test application with xml driver
     * - CONFIG_ORM_METADATA_APPLICATION = xml
     * - a existing application/src/Entity folder
     * - a existing application/config/xml.
     *
     * @covers \Concrete\Core\Database\EntityManager\Driver\ApplicationDriver::getDriver
     *
     * @throws \Exception
     */
    public function testGetXMLDriver()
    {
        // prepare
        if (!$this->filesystem->isWritable(DIR_APPLICATION . '/' . DIRNAME_CONFIG)) {
            throw new \Exception('Cannot write to the application/config directory for the testing purposes. Please check permissions!');
        }
        if (!$this->filesystem->isDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_XML)) {
            $this->filesystem->makeDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_XML);
        }
        if (!$this->filesystem->isDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES)) {
            $this->filesystem->makeDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES);
        }
        $this->configRepository->set(CONFIG_ORM_METADATA_APPLICATION, 'xml');

        // test
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $driver = $applicationDriver->getDriver();
        $this->assertEquals('xml', $this->configRepository->get(CONFIG_ORM_METADATA_APPLICATION));
        $this->assertTrue($this->filesystem->isDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
                'application/src/Entities doesn\'t exist.'));
        $this->assertInstanceOf('\Doctrine\ORM\Mapping\Driver\XmlDriver', $driver);
        // Test if the driver contains the default lookup path
        $driverPaths = $driver->getLocator()->getPaths();
        $this->assertEquals(DIR_APPLICATION . '/' . REL_DIR_METADATA_XML, $driverPaths[0]);
    }

    /**
     * Failing test for XMLDriver with missing application/config/xml directory.
     *
     * @covers \Concrete\Core\Database\EntityManager\Driver\ApplicationDriver::getDriver
     */
    public function testFailingGetXMLDriverWithNoConfigXMLDirectory()
    {
        $this->configRepository->set(CONFIG_ORM_METADATA_APPLICATION, 'xml');
        if (!$this->filesystem->isDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES)) {
            $this->filesystem->makeDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES);
        }

        $this->assertFalse($this->filesystem->isDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_XML));
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $driver = $applicationDriver->getDriver();
        // this case doesn't return a driver
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $driver);
    }

    /**
     * Test application with xml driver
     * - CONFIG_ORM_METADATA_APPLICATION = yml || yaml
     * - a existing application/src/Entity folder
     * - a existing application/config/yaml.
     *
     * @dataProvider dataProviderTestGetYMLDriver
     * @covers \Concrete\Core\Database\EntityManager\Driver\ApplicationDriver::getDriver
     *
     * @param string $setting
     *
     * @throws \Exception
     */
    public function testGetYMLDriver($setting)
    {
        // prepare
        if (!$this->filesystem->isWritable(DIR_APPLICATION . '/' . DIRNAME_CONFIG)) {
            throw new \Exception('Cannot write to the application/config directory for the testing purposes. Please check permissions!');
        }
        if (!$this->filesystem->isDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_YAML)) {
            $this->filesystem->makeDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_YAML);
        }
        if (!$this->filesystem->isDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES)) {
            $this->filesystem->makeDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES);
        }
        $this->configRepository->set(CONFIG_ORM_METADATA_APPLICATION, $setting);

        // test
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $driver = $applicationDriver->getDriver();
        $this->assertEquals($setting, $this->configRepository->get(CONFIG_ORM_METADATA_APPLICATION));
        $this->assertTrue($this->filesystem->isDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
                'application/src/Entities doesn\'t exist.'));
        $this->assertInstanceOf('\Doctrine\ORM\Mapping\Driver\YamlDriver', $driver);
        // Test if the driver contains the default lookup path
        $driverPaths = $driver->getLocator()->getPaths();
        $this->assertEquals(DIR_APPLICATION . '/' . REL_DIR_METADATA_YAML, $driverPaths[0]);
    }

    /**
     * Failing test for XMLDriver with missing application/config/yaml directory.
     *
     * @dataProvider dataProviderTestGetYMLDriver
     * @covers \Concrete\Core\Database\EntityManager\Driver\ApplicationDriver::getDriver
     *
     * @param string $setting
     */
    public function testFailingGetYMLDriverWithNoConfigYamlDirectory($setting)
    {
        $this->configRepository->set(CONFIG_ORM_METADATA_APPLICATION, $setting);
        if (!$this->filesystem->isDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES)) {
            $this->filesystem->makeDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES);
        }

        $this->assertFalse($this->filesystem->isDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_YAML));
        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $driver = $applicationDriver->getDriver();
        // this case doesn't return a driver
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $driver);
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataProviderTestGetYMLDriver()
    {
        return [
            ['yml'],
            ['yaml'],
        ];
    }

    /**
     * Test namespace.
     *
     * @dataProvider dataProviderGetNamespace
     * @covers \Concrete\Core\Database\EntityManager\Driver\ApplicationDriver::getNamespace
     *
     * @param bool $isLegacy
     * @param string $namespace
     */
    public function testGetNamespace($isLegacy, $namespace)
    {
        if ($isLegacy) {
            $this->configRepository->save('app.enable_legacy_src_namespace', true);
        }

        $applicationDriver = new ApplicationDriver($this->configRepository, $this->app);
        $this->assertEquals($namespace, $applicationDriver->getNamespace());

        if ($isLegacy) {
            $this->configRepository->save('app.enable_legacy_src_namespace', false);
        }
    }

    /**
     * Test the default and the legacy namespaces of application entites.
     *
     * @return array
     */
    public function dataProviderGetNamespace()
    {
        return [
            ['isLegacy' => true, 'namespace' => 'Application\Src'],
            ['isLegacy' => false, 'namespace' => 'Application\Entity'],
        ];
    }

    /**
     * Clean up altern folder system.
     */
    public function cleanupFolderSystem()
    {
        if ($this->filesystem->isDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES)) {
            $this->filesystem->deleteDirectory(DIR_APPLICATION . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES);
        }
        if ($this->filesystem->isDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_XML)) {
            $this->filesystem->deleteDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_XML);
        }
        if ($this->filesystem->isDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_YAML)) {
            $this->filesystem->deleteDirectory(DIR_APPLICATION . '/' . REL_DIR_METADATA_YAML);
        }
    }

    /**
     * Clean up altered config values.
     */
    public function cleanupConfig()
    {
        $this->configRepository->save('app.enable_legacy_src_namespace', false);
        $this->configRepository->set(CONFIG_ORM_METADATA_APPLICATION, '');
    }

    /**
     * Clean up if a Exception is thrown.
     *
     * @param \Exception $e
     */
    protected function onNotSuccessfulTest(\Exception $e)
    {
        $this->cleanupFolderSystem();
        $this->cleanupConfig();
    }

    /*
     * Example of mocking the concrete config repository
     */
    /*protected function getMockConcreteConfigRepository($setting){
        $configRepoStub = $this->getMockBuilder('Concrete\Core\Config\Repository\Repository')
                                ->disableOriginalConstructor()
                                ->getMock();
        $configRepoStub->method('get')
            ->will($this->onConsecutiveCalls(
                false,
                array(),
                false,
                $setting
                ));
    }*/
}

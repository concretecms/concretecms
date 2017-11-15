<?php

namespace Concrete\Tests\Database\EntityManager\Driver;

use Concrete\Core\Database\EntityManager\Driver\CoreDriver;
use Concrete\Core\Support\Facade\Application;
use PHPUnit_Framework_TestCase;

/**
 * CoreDriverTest.
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class CoreDriverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Database\EntityManager\Driver\CoreDriver
     */
    protected $coreDriver;

    /**
     * @var \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain
     */
    protected $dirverChain;

    /**
     * Setup.
     */
    public function setUp()
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
        $entityManagerConfigFactory = $this->app->make('Concrete\Core\Database\EntityManagerConfigFactory');
        $this->driverChain = $entityManagerConfigFactory->getMetadataDriverImpl();
        $this->coreDriver = new CoreDriver($this->app);
    }

    /**
     * Test if getDriver method contains the correct AnnotationDriver, a cached AnnotationReader and the correct entity lookup path.
     */
    public function testGetDriver()
    {
        $annotaionDriver = $this->coreDriver->getDriver();

        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver',
            $annotaionDriver,
            'The core metadata driver musst be an AnnotationDriver');
        $this->assertInstanceOf('Doctrine\Common\Annotations\CachedReader',
            $annotaionDriver->getReader(),
            'AnnotationReader is not cached. For performance reasons, it should be wrapped in a CachedReader');
        // Test if the driver contains the default lookup path
        $driverPaths = $annotaionDriver->getPaths();
        $this->assertEquals(DIR_BASE_CORE . '/' . DIRNAME_CLASSES . '/' . DIRNAME_ENTITIES,
            $driverPaths[0],
            'CoreDriver doesn\'t contain the correct entity lookup path.');
    }

    /**
     * Test getNamespce method returns the correct core namespace.
     */
    public function testGetNamespace()
    {
        $this->assertInstanceOf('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain',
            $this->driverChain, 'Is not a Doctrine ORM MappingDriverChain');
        $drivers = $this->driverChain->getDrivers();

        $this->assertArrayHasKey('Concrete\Core\Entity', $drivers);
    }
}

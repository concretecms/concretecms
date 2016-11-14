<?php

namespace Concrete\Tests\Core\Database\EntityManager\Provider;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider;
use Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures\PackageControllerWithgetPackageEntityPath;
use Concrete\Tests\Core\Database\EntityManager\Provider\Fixtures\PackageControllerXml;

/**
 * PackageProviderFactoryTest
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class DefaultPackageProviderTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var \Concrete\Core\Application\Application 
     */
    protected $app;
    
    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
    }
    
    /**
     * Test packages with getPackageEntityPath() method
     * 
     * @covers DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithGetPackageEntityPath()
    {
        $package = new PackageControllerWithgetPackageEntityPath($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        $this->assertInstanceOf('Doctrine\ORM\Mapping\Driver\AnnotationDriver', $dpp->getDrivers());
    }
    
    /**
     * Test package with default driver and not existing source directory
     * 
     * @covers DefaultPackageProvider::getDrivers
     */
    public function testGetDriversWithNoExistingSrcDirectory()
    {
        $package = new PackageControllerDefault($this->app);
        $dpp = new DefaultPackageProvider($this->app, $package);
        
        $drivers = $dpp->getDrivers();
        
        $this->assertInternalType('array',$drivers);
        
        var_dump($drivers);
        $this->assertEmpty($drivers);
    }
    
}

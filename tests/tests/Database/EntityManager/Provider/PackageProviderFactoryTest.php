<?php

namespace Concrete\Tests\Database\EntityManager\Provider;

use Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures\PackageControllerDefault;
use Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures\PackageControllerYaml;
use PHPUnit_Framework_TestCase;

/**
 * PackageProviderFactoryTest.
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class PackageProviderFactoryTest extends PHPUnit_Framework_TestCase
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
     * Test PackageProviderFactory if a package controller with no interfaces is passed
     * This is de default behavior.
     *
     * @covers \Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory::getEntityManagerProvider
     */
    public function testGetEntityManagerProviderDefaultBehavior()
    {
        $package = new PackageControllerDefault($this->app);
        $ppf = new PackageProviderFactory($this->app, $package);
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Provider\DefaultPackageProvider', $ppf->getEntityManagerProvider());
    }

    /**
     * Test PackageProviderFactory if a package controller with a
     * ProviderInterface interface is passed.
     *
     * @covers \Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory::getEntityManagerProvider
     */
    public function testGetEntityManagerProviderWithProviderInterface()
    {
        $package = new PackageControllerYaml($this->app);
        $ppf = new PackageProviderFactory($this->app, $package);
        $this->assertInstanceOf('Concrete\Core\Database\EntityManager\Provider\ProviderInterface', $ppf->getEntityManagerProvider());
    }

    /*
     * Test PackageProviderFactory if a package controller with a
     * ProviderAggregateInterface is passed
     *
     * @covers \Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory::getEntityManagerProvider
     */
//    public function testGetEntityManagerProviderWithProviderAggregateInterface()
//    {
//        // not yeat coverd
//    }
}

<?php

namespace Concrete\Tests\Database\EntityManager\Provider;

use Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures\PackageControllerDefault;
use Concrete\TestHelpers\Database\EntityManager\Provider\Fixtures\PackageControllerYaml;
use Concrete\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * PackageProviderFactoryTest.
 *
 * @author Markus Liechti <markus@liechti.io>
 */
#[Group('orm_setup')]
#[CoversClass(\Concrete\Core\Database\EntityManager\Provider\PackageProviderFactory::class)]
class PackageProviderFactoryTest extends TestCase
{
    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * Setup.
     */
    public function setUp():void
    {
        parent::setUp();
        $this->app = Application::getFacadeApplication();
    }

    /**
     * Test PackageProviderFactory if a package controller with no interfaces is passed
     * This is de default behavior.
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
     */
//    public function testGetEntityManagerProviderWithProviderAggregateInterface()
//    {
//        // not yeat coverd
//    }
}

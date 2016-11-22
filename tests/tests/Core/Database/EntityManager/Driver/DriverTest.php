<?php

namespace Concrete\Tests\Core\Database\EntityManager\Driver;

use Concrete\Core\Database\EntityManager\Driver\Driver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;

/**
 * DriverTest
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class DriverTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        parent::setUp();
        $driver = new YamlDriver('config/yaml');
        $this->driver = new Driver('Test\Namespace', $driver);
    }

    /**
     * @covers Driver::getDriver
     */
    public function testGetDriver()
    {
        $this->assertInstanceOf('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver', $this->driver->getDriver());
    }

    /**
     * @covers Driver::getNamespace
     */
    public function testGetNamespace()
    {
        $this->assertEquals('Test\Namespace', $this->driver->getNamespace());
    }

}

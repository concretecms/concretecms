<?php

namespace Concrete\Tests\Database\EntityManager\Driver;

use Concrete\Core\Database\EntityManager\Driver\Driver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use PHPUnit_Framework_TestCase;

/**
 * DriverTest.
 *
 * @author Markus Liechti <markus@liechti.io>
 * @group orm_setup
 */
class DriverTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $driver = new YamlDriver('config/yaml');
        $this->driver = new Driver('Test\Namespace', $driver);
    }

    /**
     * @covers \Concrete\Core\Database\EntityManager\Driver\Driver::getDriver
     */
    public function testGetDriver()
    {
        $this->assertInstanceOf('Doctrine\Common\Persistence\Mapping\Driver\MappingDriver', $this->driver->getDriver());
    }

    /**
     * @covers \Concrete\Core\Database\EntityManager\Driver\Driver::getNamespace
     */
    public function testGetNamespace()
    {
        $this->assertEquals('Test\Namespace', $this->driver->getNamespace());
    }
}

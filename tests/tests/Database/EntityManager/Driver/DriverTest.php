<?php

namespace Concrete\Tests\Database\EntityManager\Driver;

use Concrete\Core\Database\EntityManager\Driver\Driver;
use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Concrete\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * DriverTest.
 *
 * @author Markus Liechti <markus@liechti.io>
 */
#[Group('orm_setup')]
#[CoversClass(Driver::class)]
class DriverTest extends TestCase
{
    /**
     * @var Driver
     */
    protected $driver;

    public function setUp():void
    {
        parent::setUp();
        $driver = new YamlDriver('config/yaml');
        $this->driver = new Driver('Test\Namespace', $driver);
    }

    public function testGetDriver()
    {
        $this->assertInstanceOf('Doctrine\Persistence\Mapping\Driver\MappingDriver', $this->driver->getDriver());
    }

    public function testGetNamespace()
    {
        $this->assertEquals('Test\Namespace', $this->driver->getNamespace());
    }
}

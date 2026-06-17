<?php

namespace Concrete\Tests\Database\Driver;

use Concrete\Core\Database\Driver\DriverManager;
use Concrete\Tests\TestCase;

class DriverManagerTest extends TestCase
{
    /** @var DriverManager */
    protected $driverManager;

    public function setUp():void
    {
        $this->driverManager = new DriverManager(\Core::getFacadeRoot());
    }

    public function testConfigLoad()
    {
        $testMock = \Mockery::namedMock('ConfigExtensionNewable');
        $this->driverManager->configExtensions(
            [
                'test' => $testMock->mockery_getName(),
            ]
        );

        $this->assertInstanceOf($testMock->mockery_getName(), $this->driverManager->driver('test'));
    }
}

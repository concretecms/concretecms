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
        $mock = \Mockery::mock(\Doctrine\DBAL\Driver::class);
        $this->driverManager->configExtensions(
            [
                'test' => $mock::class,
            ]
        );

        $this->assertInstanceOf($mock::class, $this->driverManager->driver('test'));
    }
}

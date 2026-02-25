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
        $this->driverManager->configExtensions(
            [
                'test' => __CLASS__,
            ]
        );

        $this->assertInstanceOf(__CLASS__, $this->driverManager->driver('test'));
    }
}

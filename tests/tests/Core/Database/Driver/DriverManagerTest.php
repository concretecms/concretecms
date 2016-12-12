<?php

use Concrete\Core\Database\Driver\DriverManager;

class DriverManagerTest extends PHPUnit_Framework_TestCase
{
    /** @var DriverManager */
    protected $driverManager;

    public function setUp()
    {
        $this->driverManager = new DriverManager(\Core::getFacadeRoot());
    }

    public function testConfigLoad()
    {
        $this->driverManager->configExtensions(
            array(
                'test' => 'DriverManagerTest', ));

        $this->assertInstanceOf('DriverManagerTest', $this->driverManager->driver('test'));
    }
}
